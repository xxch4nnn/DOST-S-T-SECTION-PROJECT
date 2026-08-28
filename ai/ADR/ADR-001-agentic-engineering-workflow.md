# ADR-001: Agentic Engineering Workflow

> **Date:** 2026-07-14
> **Status:** Accepted
> **Deciders:** Backend Developer

---

## Context

This project is built by a solo developer learning Laravel while also building a production system (DOST Scholarship Records Digitization). The developer needs to maximize learning velocity while maintaining professional-quality output.

## Decision

Adopt an **agentic engineering workflow** with 10 specialized AI agents, each owning a specific part of the software lifecycle:

1. Solution Architect — designs systems
2. Senior Mentor — teaches concepts
3. Laravel Implementer — writes code
4. QA Engineer — generates tests
5. Documentation Agent — writes docs
6. Security Reviewer — audits vulnerabilities
7. Database Reviewer — reviews schemas
8. Code Reviewer — reviews code quality
9. DevOps Engineer — handles infrastructure
10. Product Owner — manages scope
11. Knowledge Archivist — extracts lessons and evolves AI rules

All agents share persistent context via memory files in the `ai/` folder.

A strict pipeline enforces the rules:
1. **No AI may write code until another AI has reviewed the design.**
2. **Continuous Learning Loop:** The final step of any feature pipeline MUST invoke the **Knowledge Archivist**. The Archivist will extract lessons learned and autonomously update `SKILL.md` files or generate Knowledge Items to ensure the team gets smarter over time.

## Consequences

### Positive
- Each agent is focused and produces higher-quality output
- The pipeline prevents skipping critical steps (security, testing)
- Memory files keep agents consistent across sessions
- The developer learns engineering process, not just syntax
- Scales naturally as the team grows

### Negative
- More initial setup overhead
- Slower per-feature velocity (intentional — trades speed for quality)
- Requires discipline to follow the pipeline
- Memory files must be kept in sync with actual code

### Risks
- Agent fatigue — might skip steps when feeling confident (mitigation: treat the pipeline as non-negotiable)
- Stale memory — files drift from reality (mitigation: Documentation Agent updates after every merge)
