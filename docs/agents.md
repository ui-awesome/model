# AI assistance

The canonical package skill is [ui-awesome-model](../skills/ui-awesome-model/SKILL.md). It covers typed model mapping and nested values. The checkout's
`.agents/skills/` and `.claude/skills/` links point to that single source; no client-specific copies are maintained.

## Install in an application

Use the [one-time Composer setup](https://yii2-framework.github.io/docs/project/ai-agents/#composer-installation)
for `llm/skills`, explicitly trust `ui-awesome/model`, and configure auto-sync before expecting dependency updates to
synchronize skills. Composer metadata alone does not install a synchronizer or activate a skill. Inspect the installed
plugin version, permitted donor and synchronization result.

```bash
composer install
composer skills:show
```

With manual synchronization configured, run `composer skills:update`. These commands assume the application setup is
already authorized and complete. Installing a package under `vendor/` alone does not expose a skill at the application root.

For optional project-scoped installation, list the local source first:

```bash
npx skills@1.7.2 add /absolute/path/to/model --list
npx skills@1.7.2 add /absolute/path/to/model --skill ui-awesome-model --agent claude-code codex opencode
```

Use an installed `vendor/ui-awesome/model` source only if that release contains this skill. A copied skill must be
refreshed on upgrades. Local changes do not prove availability from a published GitHub or Composer release.

## Discovery and activation

Open a fresh session at the project root. Codex uses `.agents/skills/ui-awesome-model/` and explicit `$ui-awesome-model`;
Claude Code uses `.claude/skills/ui-awesome-model/` and `/ui-awesome-model`. OpenCode can load the named skill through its native skill tool.
Verify the installed client's actual discovery paths and avoid duplicate/global same-name fallbacks. Installer `--list`
checks source discovery, not native activation. Capture a real load event and execute the task before calling it tested.

## Example prompts

Explicit Codex invocation (adapt the first line to the other client's native mechanism):

```text
$ui-awesome-model
Define a BaseModel with a mapped input key and a trimmed name. Load scoped data, inspect values and serialize it; add a present-null nested path and clone-isolation check.
Use the installed source, show executable checks, and do not install dependencies or alter unrelated code.
```

Natural selection, without naming the skill:

```text
How do I map an incoming key to a typed property and distinguish missing from nullable values with ui-awesome/model?
Include a runnable normal case, a boundary case and a deliberate invalid-input or unsupported-capability case.
```

Expected verification: inspect the referenced source contracts, execute the generated PHP with independent assertions,
and record exact output or exceptions. Check state isolation and encoding where applicable. A correct answer without a
native load trace is not evidence of native activation. In a separate unrelated task, ensure this skill is not forced
into scope; do not change the real checkout to run a negative control.

## Verification limits

Run this package's configured checks and the consuming application's relevant integration tests. Mark PHP versions,
client activation, browser behavior and published artifacts separately when not exercised. Keep raw model evaluations,
fixtures and provider traces in ignored maintainer storage, not the distributable skill. The examples above are prompts,
not a claim that a particular provider has executed them.

[Back to documentation](../README.md#documentation)
