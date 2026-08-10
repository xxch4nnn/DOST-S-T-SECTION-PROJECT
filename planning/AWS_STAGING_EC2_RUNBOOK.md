# DOSTorage V1 — EC2 Staging Sandbox Runbook

**Purpose:** Stand up a **single-EC2** staging box (Nginx + PHP 8.3-FPM + MySQL 8 + Node 22) for testing.  
**Not production:** Full ECS/RDS design remains in [`AWS_PRODUCTION_DEPLOYMENT_PROMPT.md`](AWS_PRODUCTION_DEPLOYMENT_PROMPT.md) and stays blocked on the Decision Gate. This sandbox does **not** clear production deploy.

**Region default:** `ap-southeast-1`  
**Instance default:** `t3.micro` (or `t2.micro`), Ubuntu 24.04 LTS  
**App path on host:** `/var/www/dostorage`  
**Repo:** `https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT.git`

---

## Human-in-the-loop checklist

Only you (Console / SSH / GitHub settings) can do these. Agent cannot create your AWS account or click the Console.

| # | You must do | When |
|---|-------------|------|
| H1 | Create/sign in to AWS; enable **root MFA**; set a **Billing budget** alert (e.g. $10) | Before any resources |
| H2 | Create IAM user + access keys; save `.pem` key pair privately | Before EC2 |
| H3 | Launch EC2, security group, Elastic IP | Step 3 |
| H4 | SSH in and run install/bootstrap commands (or paste from this runbook) | Steps 4–6 |
| H5 | Choose how to clone the private repo (deploy key or PAT) — never commit secrets | Step 5 |
| H6 | Set strong MySQL + `.env` secrets on the box | Step 5 |
| H7 | (Optional) DNS A record + Certbot HTTPS | Step 7 |
| H8 | GitHub → Settings → Environments → `staging` → secret `STAGING_URL` | Step 8 |
| H9 | Run Actions → **Staging / Production deploy (gated)** → target `staging`, `confirm_aws_gates=APPROVED` for **sandbox smoke only** | After `/health` works |
| H10 | Stop/terminate the instance when idle to control cost | Ongoing |

After H8–H9, reply in chat with `STAGING_URL` (or “health OK”) so we can confirm the gated workflow and next deploy automation.

---

## Step 1 — Account + billing guardrails

1. Sign up / sign in at [aws.amazon.com](https://aws.amazon.com).
2. Root user → **Security credentials** → enable **MFA**.
3. **Billing** → **Budgets** → create a monthly budget (e.g. `$10`) with email alert at 80% / 100%.
4. Prefer **stop** (not only logout) when the staging box is unused.

---

## Step 2 — IAM user (do not use root day-to-day)

1. **IAM** → Users → Create user (e.g. `dostorage-staging-admin`).
2. Attach policy **AdministratorAccess** (acceptable for a sandbox you alone control).
3. Create **Access key** (CLI) if you will use AWS CLI later. Store outside the repo.
4. Never put root or IAM keys in git, `.env` committed files, or chat logs.

---

## Step 3 — Launch EC2

**Console → EC2 → Launch instance**

| Setting | Value |
|---------|--------|
| Name | `dostorage-staging` |
| AMI | Ubuntu Server 24.04 LTS |
| Instance type | `t3.micro` (or `t2.micro`) |
| Key pair | Create new → download `dostorage-staging.pem` → store privately |
| Network | Default VPC OK for sandbox |
| Storage | 20 GiB gp3 is enough to start |

**Security group inbound**

| Port | Source | Notes |
|------|--------|--------|
| 22 | Your public IP `/32` | Tighten; do not leave `0.0.0.0/0` if avoidable |
| 80 | `0.0.0.0/0` (or office/VPN IP) | HTTP |
| 443 | `0.0.0.0/0` (or office/VPN IP) | HTTPS (after Certbot) |

**Elastic IP:** Allocate → Associate with the instance (address survives stop/start).

**SSH from your PC (PowerShell example):**

```powershell
icacls $env:USERPROFILE\.ssh\dostorage-staging.pem /inheritance:r /grant:r "$($env:USERNAME):(R)"
ssh -i $env:USERPROFILE\.ssh\dostorage-staging.pem ubuntu@<ELASTIC_IP>
```

---

## Step 4 — Install the stack (on the instance)

Run as `ubuntu` (use `sudo` where shown):

```bash
sudo apt-get update
sudo apt-get upgrade -y

# PHP 8.3 + Nginx + MySQL client libs
sudo apt-get install -y software-properties-common curl ca-certificates gnupg unzip git
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y \
  nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

# Node 22 (NodeSource)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs
node -v && npm -v
```

Secure MySQL (set a root password / auth method you will remember):

```bash
sudo mysql_secure_installation
```

---

## Step 5 — MySQL database + clone + Laravel bootstrap

### 5.1 Database

```bash
sudo mysql -e "
CREATE DATABASE dostorage_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dostorage'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON dostorage_staging.* TO 'dostorage'@'localhost';
FLUSH PRIVILEGES;
"
```

### 5.2 Clone (pick one)

**Option A — Deploy key (recommended):** GitHub repo → Settings → Deploy keys → add read-only key from the instance (`ssh-keygen -t ed25519 -C "dostorage-staging"`).

```bash
sudo mkdir -p /var/www
sudo chown ubuntu:ubuntu /var/www
git clone git@github.com:xxch4nnn/DOST-S-T-SECTION-PROJECT.git /var/www/dostorage
cd /var/www/dostorage
git checkout master   # or your staging branch
```

**Option B — HTTPS + PAT:** use a fine-scoped token; do not write it into the repo or shell history if you can avoid it (`GIT_ASKPASS` / credential helper).

### 5.3 Dependencies + env

```bash
cd /var/www/dostorage
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

cp .env.example .env
nano .env   # or vim
```

Minimum `.env` staging values:

```env
APP_NAME=DOSTorage
APP_ENV=staging
APP_DEBUG=false
APP_URL=http://<ELASTIC_IP>

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dostorage_staging
DB_USERNAME=dostorage
DB_PASSWORD=CHANGE_ME_STRONG_PASSWORD

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

Then:

```bash
php artisan key:generate
php artisan migrate --force --seed
```

Seeded login (staging only): `test@example.com` / `password`. Change or disable before sharing the URL widely.

---

## Step 6 — Nginx + permissions + caches

### 6.1 Site config

```bash
sudo tee /etc/nginx/sites-available/dostorage <<'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/dostorage/public;
    index index.php;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo ln -sf /etc/nginx/sites-available/dostorage /etc/nginx/sites-enabled/dostorage
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### 6.2 Permissions

```bash
cd /var/www/dostorage
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
# Keep ubuntu able to deploy:
sudo usermod -aG www-data ubuntu
sudo chown -R ubuntu:www-data /var/www/dostorage
```

### 6.3 Laravel optimize + smoke

```bash
cd /var/www/dostorage
php artisan config:cache
php artisan route:cache
php artisan view:cache

curl -fsS http://127.0.0.1/health
curl -fsS http://<ELASTIC_IP>/health
```

Expect JSON containing `"status":"ok"`.

Also open `http://<ELASTIC_IP>/login` in a browser.

---

## Step 7 — (Optional) Subdomain + HTTPS

1. DNS: A record `staging.<yourdomain>` → Elastic IP (wait for propagation).
2. On the instance:

```bash
sudo apt-get install -y certbot python3-certbot-nginx
sudo certbot --nginx -d staging.<yourdomain>
```

3. Set `APP_URL=https://staging.<yourdomain>` in `.env`, then:

```bash
php artisan config:cache
```

---

## Step 8 — Deploy flow + GitHub `STAGING_URL`

### 8.1 On-box deploy script

After the repo is on the instance (and this script exists on `master`):

```bash
cd /var/www/dostorage
chmod +x scripts/deploy-staging.sh
./scripts/deploy-staging.sh
```

Or pull latest then run the same script after each merge you want on staging.

### 8.2 GitHub Environment secret

1. Repo → **Settings** → **Environments** → create/select **`staging`**.
2. Add secret **`STAGING_URL`** = `http://<ELASTIC_IP>` or `https://staging.<yourdomain>` (no trailing slash).
3. Actions → **Staging / Production deploy (gated)** → Run workflow:
   - `target`: `staging`
   - `confirm_aws_gates`: `APPROVED` (sandbox remote smoke only; still not production IaC)

Workflow: [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml). It curls `${STAGING_URL}/health` and expects `"status":"ok"`.

Production target remains blocked until Decision Gate + `infrastructure/aws/` exist. Do **not** set `ALLOW_PRODUCTION_DEPLOY=true` for this sandbox.

---

## Cost / safety notes

- Stop the instance when not testing.
- Restrict SSH to your IP; rotate the `.pem` if shared.
- Staging seed credentials are for internal smoke only.
- Prefer Elastic IP + HTTPS before putting real scholar data on the box.
- This path does **not** satisfy DOST production network/isolation requirements by itself.

---

## Rollback

| Action | How |
|--------|-----|
| App bad deploy | `cd /var/www/dostorage && git checkout <good-sha> && ./scripts/deploy-staging.sh` |
| Stop charges | EC2 → Stop instance (Elastic IP stays associated if you keep the allocation) |
| Tear down | Terminate instance; release Elastic IP; delete unused volumes/key pairs |

---

## Done when

- [ ] Root MFA + budget alert on
- [ ] `curl <STAGING_URL>/health` → `"status":"ok"`
- [ ] `/login` loads; seeded admin can sign in
- [ ] GitHub `STAGING_URL` set; gated staging workflow green
