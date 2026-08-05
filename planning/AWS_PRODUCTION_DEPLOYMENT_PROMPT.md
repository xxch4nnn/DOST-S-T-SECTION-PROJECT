# DOSTorage V1 — Production Deployment Prompt for AWS

**Role:** Senior Cloud Architect + DevOps Engineer  
**Project:** DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)  
**Stack:** Laravel 13 · Livewire 4 · Spatie Permission v8 · Bootstrap 5 · Vite 8 · PHP 8.3 · MySQL 8.4  
**Constraint:** Must run entirely within DOST internal office network; offline-first; no public cloud exposure without explicit PM approval.

**Path (locked 2026-08-05):** **Both — sequenced.** Keep this document as AWS design SoT only. Do **not** provision AWS until Decision Gate clears. Ship CI synthetic smoke + gated deploy workflow first (`synthetic-smoke` in `.github/workflows/test.yml`, manual `.github/workflows/deploy.yml`).

| Step | Status |
|------|--------|
| A. CI synthetic smoke (health / auth / upload / download + live `/health` curl) | Required now |
| B. Manual staging deploy workflow (secrets + `confirm_aws_gates=APPROVED`) | Scaffolded; remote curl when `STAGING_URL` set |
| C. AWS Phases 1–6 / IaC under `infrastructure/aws/` | **Blocked** on Decision Gate |

---

## Objective

Design and execute a production deployment strategy for DOSTorage V1 on AWS that satisfies:
- Live user access for DOST-SEI staff
- Secure file upload/download with audit trail
- Real-time database transactions with 99.5%+ uptime
- Full compliance with DOST data residency requirements
- Zero-trust security posture

---

## Phase 1: Compute & High Availability

### 1.1 Compute Layer
- **Primary:** AWS ECS on Fargate or EKS on EC2
  - Minimum 2 tasks/pods across 2 AZs
  - Auto Scaling: target CPU 70%, scale out at 80%, scale in at 40%
  - Health check: `/health` endpoint every 30s, unhealthy threshold 3
  - Rolling update: 2 tasks minimum healthy during deploy

### 1.2 Load Balancing
- **ALB** with:
  - HTTPS only (TLS 1.2+)
  - Path-based routing: `/` → Laravel app, `/api/*` → API layer
  - Sticky sessions disabled (Laravel handles session via database/Redis)
  - WebSocket support for Livewire updates if needed

### 1.3 Auto Scaling & Resilience
- Target tracking: CPU 70%, memory 75%
- Cooldown: 300s scale out, 600s scale in
- Lifecycle hooks for graceful drain
- Multi-AZ deployment mandatory

---

## Phase 2: File Storage & Security

### 2.1 Storage Layer
- **S3 Private Bucket** with:
  - Server-side encryption: AES-256 or KMS
  - Versioning enabled
  - Object lock: compliance mode 30 days
  - Lifecycle policy: Glacier after 1 year, delete after 7 years
  - Bucket policy: deny unencrypted uploads, deny non-VPC access

### 2.2 File Access Flow
- Upload: Laravel → pre-signed S3 URL → direct browser→S3 upload
- Download: Laravel validates permission → generates expiring pre-signed URL (60s)
- Never expose S3 credentials to client
- MIME validation: PDF, JPG, PNG only; max 10MB

### 2.3 Backup Strategy
- Daily automated snapshot of storage bucket
- Cross-region replication to secondary AWS region
- Backup retention: 30 days local, 1 year Glacier

---

## Phase 3: Database & Caching

### 3.1 Primary Database
- **RDS MySQL 8.4**:
  - Instance class: db.t3.large minimum
  - Multi-AZ deployment
  - Storage: 100GB gp3, auto-scale to 500GB
  - Backup: daily automated, 7-day retention
  - Parameter group: tuned for Laravel (wait_timeout=60, max_connections=200)

### 3.2 Read Replicas
- 1 read replica in secondary AZ
- Route all report/read queries to replica
- Replication lag monitoring: alert if > 5s

### 3.3 Caching Layer
- **ElastiCache Redis 7.x**:
  - Cluster mode: 2 shards, 1 replica each
  - Use cases: session store, permission cache, query cache, rate limiting
  - TTL: 1 hour default, 24 hours for static lookups
  - Persistence: RDB snapshots every 6 hours

### 3.4 Migration Strategy
- Use Laravel migrations with zero-downtime deploy pattern
- Additive schema changes only; no drops in production
- Migration window: 02:00–04:00 PHT, automated via CI/CD

---

## Phase 4: Security, Networking & Environment Control

### 4.1 Network Architecture
- **VPC** with:
  - Public subnet: ALB, NAT Gateway
  - Private subnet: ECS tasks/EKS pods, RDS, ElastiCache
  - Isolated subnet: No direct internet access
- Security Groups:
  - ALB: 80/443 from 0.0.0.0/0
  - App: 80 from ALB SG only
  - DB: 3306 from app SG only
  - Redis: 6379 from app SG only

### 4.2 Identity & Access
- IAM roles per service (task role, execution role)
- No hardcoded credentials; use AWS Secrets Manager
- Rotate DB credentials every 90 days
- MFA required for AWS console access

### 4.3 Application Security
- HTTPS enforced at ALB; HTTP → 301 redirect
- Security headers: CSP, HSTS, X-Frame-Options, X-XSS-Protection
- Rate limiting: 100 req/min per IP via ALB WAF
- WAF rules: block SQL injection, XSS, known bad bots
- File upload scanning: ClamAV or AWS Inspector

### 4.4 Secrets Management
- AWS Secrets Manager for:
  - DB credentials
  - App key
  - Spatie permission cache key
  - S3 credentials
- Inject via ECS task definition or EKS env vars
- Never commit secrets to repo

---

## Phase 5: CI/CD & Monitoring

### 5.1 CI/CD Pipeline
- **GitHub Actions**:
  - Trigger: push to `master` or tag `v*`
  - Jobs:
    1. `test` — PHPUnit + Pint + lint-css
    2. `build` — Docker build, push to ECR
    3. `deploy-staging` — auto-deploy to staging ECS/EKS
    4. `deploy-prod` — manual approval gate, deploy to prod
  - Rollback: automatic on health check failure
  - Artifact retention: 30 days

### 5.2 Monitoring & Alerting
- **CloudWatch**:
  - Metrics: CPU, memory, request count, 5xx rate, DB connections, Redis hit rate
  - Alarms:
    - 5xx rate > 1% for 5m → PagerDuty/Slack
    - CPU > 80% for 10m → scale out
    - DB connections > 180 → warning
    - Disk usage > 80% → critical
- **X-Ray** or **OpenTelemetry** for distributed tracing
- **Synthetics** canary: ping `/health` every 1m from 3 regions

### 5.3 Logging
- CloudWatch Logs for app, nginx, php-fpm
- Structured JSON logging with request ID
- Retention: 30 days hot, 1 year cold
- Alert on error log spike > 3x baseline

### 5.4 Backup & Disaster Recovery
- RDS automated backups + transaction logs
- S3 cross-region replication
- ECS/EKS task definitions versioned in Git
- DR runbook in `planning/DISASTER_RECOVERY.md`
- RTO target: 4 hours, RPO target: 1 hour

---

## Phase 6: Compliance & Data Residency

### 6.1 DOST Requirements
- All data must remain in Philippines region (ap-southeast-1 or ap-southeast-2)
- No cross-border data transfer without explicit approval
- Audit trail: all file access logged to CloudWatch + local DB
- Retention: 7 years per DOST policy

### 6.2 Cost Controls
- Budget alarm: $500/month threshold
- Right-size instances monthly
- Use Savings Plans for steady-state workloads
- S3 Intelligent-Tiering for infrequent access

---

## Execution Checklist

- [ ] VPC, subnets, security groups created
- [ ] RDS MySQL 8.4 provisioned with multi-AZ
- [ ] ElastiCache Redis cluster deployed
- [ ] S3 bucket with encryption, versioning, lifecycle
- [ ] ALB with HTTPS, WAF, target group
- [ ] ECS/EKS cluster with auto scaling
- [ ] Secrets Manager populated with all credentials
- [ ] GitHub Actions CI/CD pipeline configured
- [ ] CloudWatch alarms and dashboards deployed
- [ ] DR runbook written and tested
- [ ] Load test: simulate 500 concurrent users, p95 < 2s
- [ ] Security scan: OWASP ZAP baseline passed
- [ ] Compliance review: data residency confirmed

---

## Outputs

| Artifact | Location |
|----------|----------|
| Terraform / CloudFormation IaC | `infrastructure/aws/` |
| CI/CD workflow | `.github/workflows/deploy.yml` |
| Runbook | `planning/AWS_PRODUCTION_RUNBOOK.md` |
| Cost estimate | `planning/AWS_COST_ESTIMATE.md` |
| DR plan | `planning/DISASTER_RECOVERY.md` |

---

## Decision Gate

Before provisioning AWS (Phases 1–6 / `infrastructure/aws/`), confirm:
1. DOST approves AWS cloud hosting (network isolation requirements)
2. Budget approved for ~$400–600/month steady state
3. Data residency in Philippines region accepted
4. DR RTO/RPO targets agreed
5. CI `synthetic-smoke` is green on `master` and Dependabot majors still follow Paths A/B/C in `AGENTS.md`

If any gate fails, pivot to on-premises Docker swarm or local VM cluster instead; keep GitHub Actions synthetic smoke as the live-prod testing path.

**Manual deploy:** Actions → “Staging / Production deploy (gated)” → `confirm_aws_gates=APPROVED` only after items 1–4. Production also requires secret `ALLOW_PRODUCTION_DEPLOY=true`.
