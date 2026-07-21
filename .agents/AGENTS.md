# Global Workspace Rules

This file intercepts the AI system and enforces project-specific constraints on all agents working within this workspace.

## 1. The Continuous Learning Imperative
- Before designing or implementing a feature, agents **MUST** check the system's knowledge base and read their own `SKILL.md` for `Evolved Rules (Learned from Experience)`.
- If an agent solves a complex bug or makes a structural decision, it must hand off execution to the **Knowledge Archivist** (`archivist`) to document the lesson.

## 2. Default Assumptions for DOST Bootcamps
- Always assume the project uses Laravel. 
- All raw PHP scripts belong in the `index.php` portal or designated `week-1` folders. 
- UI should always utilize glassmorphic styles unless requested otherwise.
