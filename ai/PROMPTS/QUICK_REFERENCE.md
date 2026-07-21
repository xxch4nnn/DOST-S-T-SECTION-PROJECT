# Agent Invocation Quick Reference

> How to invoke each agent in Antigravity IDE

---

## The Agents

Each agent is a **skill** installed globally. Antigravity IDE will automatically detect and invoke them based on trigger phrases.

### 1. Solution Architect
```
"Architect this feature: Scholar document upload"
"Design the architecture for role-based access control"
"Create an ADR for the file storage strategy"
```

### 2. Senior Mentor
```
"Explain how Laravel middleware works"
"Teach me about Eloquent relationships"
"Review my understanding of dependency injection"
```

### 3. Laravel Implementer
```
"Implement the ScholarController with CRUD"
"Create the migration for the documents table"
"Build a Livewire search component for scholars"
```

### 4. QA Engineer
```
"Generate test cases for scholar creation"
"QA the document upload feature"
"Write Pest tests for authentication"
```

### 5. Documentation Agent
```
"Document the scholars API routes"
"Write the deployment guide"
"Update the database documentation"
```

### 6. Security Reviewer
```
"Security review the document upload controller"
"Audit the authentication flow"
"Check for mass assignment vulnerabilities"
```

### 7. Database Reviewer
```
"Review this migration before I run it"
"Check my schema for normalization issues"
"Are my indexes correct for the scholars table?"
```

### 8. Code Reviewer
```
"Review this code before I merge"
"Code review the ScholarService class"
"Check my Livewire component for anti-patterns"
```

### 9. DevOps Engineer
```
"Set up Docker for this Laravel project"
"Help me configure docker-compose for MySQL"
"Debug this container networking issue"
```

### 10. Product Owner
```
"Is this feature worth building right now?"
"Prioritize the remaining features"
"Write user stories for the document upload feature"
```

---

## Pipeline Cheat Sheet

For every new feature, follow this order:

```
1. Product Owner    → "Is this in scope? Write the user story."
2. Solution Architect → "Design the architecture for this."
3. Database Reviewer  → "Review the migration I'm about to create."
4. Laravel Implementer → "Implement the approved design."
5. QA Engineer        → "Generate tests for this feature."
6. Security Reviewer  → "Audit the implementation."
7. Code Reviewer      → "Review the code quality."
8. YOU                → Read, understand, and merge.
9. Documentation      → "Update the docs."
```
