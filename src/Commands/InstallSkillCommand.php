<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallSkillCommand extends Command
{
    private const TOOLS = [
        'Claude Code',
        'Cursor',
        'GitHub Copilot',
        'Antigravity',
    ];

    // Cursor: rules that are always injected regardless of file context
    private const CURSOR_ALWAYS_APPLY = [
        'SKILL',
        'anti-patterns',
        'code-quality',
    ];

    protected $signature = 'api-skill:install
                            {tool? : The AI tool to install for (Claude Code, Cursor, GitHub Copilot, Antigravity)}
                            {--force : Overwrite existing files without asking}';

    protected $description = 'Install the Laravel API Tool Kit skill into your project for use with AI coding agents';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ( ! $this->files->isDirectory($this->sourcePath())) {
            $this->error('Skill source not found. Please reinstall essa/api-tool-kit.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('  <info>Laravel API Tool Kit — Skill Installer</info>');
        $this->newLine();

        $tool = $this->argument('tool') ?: $this->choice('Which AI coding tool are you using?', self::TOOLS);

        $this->newLine();

        return match ($tool) {
            'Claude Code', 'claude' => $this->installForClaude(),
            'Cursor', 'cursor'      => $this->installForCursor(),
            'GitHub Copilot', 'copilot' => $this->installForCopilot(),
            'Antigravity', 'antigravity' => $this->installForAntigravity(),
            default => $this->handleInvalidTool($tool),
        };
    }

    private function handleInvalidTool(string $tool): int
    {
        $this->error("Invalid tool: {$tool}. Available tools: " . implode(', ', self::TOOLS));

        return self::FAILURE;
    }

    // -------------------------------------------------------------------------
    // Tool-specific installers
    // -------------------------------------------------------------------------

    private function installForClaude(): int
    {
        // Claude Code standard (2025):
        //   Rules  → .claude/rules/**/*.md  (auto-loaded by Claude Code, supports paths: frontmatter)
        //   Skills → .claude/skills/<name>/SKILL.md  (become native slash commands: /code-review, /investigate)
        $rulesTarget  = '.claude/rules/laravel-api-tool-kit';
        $skillsTarget = '.claude/skills';

        if ( ! $this->shouldProceed($rulesTarget)) {
            return self::SUCCESS;
        }

        // 1. Copy SKILL.md (overview + project defaults) into rules dir
        $this->files->ensureDirectoryExists(base_path($rulesTarget));
        $this->files->copy(
            $this->sourcePath() . '/SKILL.md',
            base_path($rulesTarget . '/_overview.md')
        );

        // 2. Copy all rule files → .claude/rules/laravel-api-tool-kit/ (auto-loaded)
        foreach ($this->files->files($this->sourcePath() . '/rules') as $file) {
            $this->files->copy($file->getPathname(), base_path($rulesTarget . '/' . $file->getFilename()));
        }

        // 3. Copy knowledge template → .claude/knowledge/ (separate from rules)
        $this->files->copyDirectory(
            $this->sourcePath() . '/knowledge',
            base_path('.claude/knowledge')
        );

        // 4. Convert each workflow → a real Claude Code skill (native slash command)
        foreach ($this->files->files($this->sourcePath() . '/workflows') as $file) {
            $skillName = $file->getFilenameWithoutExtension();
            $skillDir  = base_path("{$skillsTarget}/{$skillName}");

            $this->files->ensureDirectoryExists($skillDir);
            $this->files->put(
                $skillDir . '/SKILL.md',
                $this->buildClaudeSkill($skillName, $file->getContents())
            );
        }

        $this->appendToClaudeMd();

        $this->success("{$rulesTarget} + {$skillsTarget}/", [
            'Rules auto-load from <comment>.claude/rules/laravel-api-tool-kit/</comment> — no manual setup needed',
            'Workflows are now native slash commands: <comment>/code-review</comment>, <comment>/investigate</comment>, etc.',
            'Fill in your primary key type in <comment>.claude/rules/laravel-api-tool-kit/_overview.md</comment> → Project Defaults',
            'Knowledge template at <comment>.claude/knowledge/_TEMPLATE.md</comment>',
        ]);

        return self::SUCCESS;
    }

    private function installForCursor(): int
    {
        // Cursor standard (2025): .cursor/rules/**/*.mdc with YAML frontmatter
        //   alwaysApply: true  → Always Apply  (injected in every session)
        //   globs: ["**/*.php"] → Auto Attached (triggers when PHP files are in context)
        //   no globs, no always → Manual        (only when explicitly @mentioned)
        $target = '.cursor/rules/laravel-api-tool-kit';

        if ( ! $this->shouldProceed($target)) {
            return self::SUCCESS;
        }

        $targetPath = base_path($target);
        $this->files->ensureDirectoryExists($targetPath);
        $this->copyAsMdc($this->sourcePath(), $targetPath);

        // Knowledge template → knowledge/ at project root (accessible to all tools)
        $this->files->copyDirectory(
            $this->sourcePath() . '/knowledge',
            base_path('knowledge')
        );

        $this->success($target, [
            'Core rules (SKILL, anti-patterns, code-quality) → <comment>Always Apply</comment> (every session)',
            'Other rule files → <comment>Auto Attached</comment> on <comment>**/*.php</comment> files',
            'Workflows & knowledge → <comment>Manual</comment> (reference with <comment>@workflow-name</comment>)',
            'Fill in your primary key type in <comment>.cursor/rules/laravel-api-tool-kit/SKILL.mdc</comment> → Project Defaults',
            'Knowledge template at <comment>knowledge/_TEMPLATE.md</comment>',
        ]);

        return self::SUCCESS;
    }

    private function installForCopilot(): int
    {
        // Copilot standard (2025):
        //   .github/copilot-instructions.md       → always-on project rules (all contexts)
        //   .github/instructions/*.instructions.md → path-scoped rules via applyTo: frontmatter
        $globalTarget = '.github/copilot-instructions.md';
        $phpTarget    = '.github/instructions/laravel-api-tool-kit.instructions.md';

        $this->files->ensureDirectoryExists(base_path('.github'));
        $this->files->ensureDirectoryExists(base_path('.github/instructions'));

        // Global rules (SKILL overview + anti-patterns + code-quality) → always-on
        $this->smartUpdate(base_path($globalTarget), $this->compileCopilotGlobal());

        // PHP-specific rules → auto-attached on *.php files
        $this->files->put(base_path($phpTarget), $this->compileCopilotPhpInstructions());

        // Knowledge template → knowledge/ at project root (accessible to all tools)
        $this->files->copyDirectory(
            $this->sourcePath() . '/knowledge',
            base_path('knowledge')
        );

        $this->success('.github/', [
            'Always-on rules in <comment>.github/copilot-instructions.md</comment>',
            'PHP-specific rules in <comment>.github/instructions/laravel-api-tool-kit.instructions.md</comment> (auto-attached to <comment>**/*.php</comment>)',
            'Fill in your primary key type in <comment>copilot-instructions.md</comment> → Project Defaults',
            'Knowledge template at <comment>knowledge/_TEMPLATE.md</comment>',
        ]);

        return self::SUCCESS;
    }

    private function installForAntigravity(): int
    {
        // Antigravity standard (2025):
        //   AGENTS.md              → cross-tool shared rules (auto-loaded, root of project)
        //   GEMINI.md              → Antigravity-native overrides (higher priority than AGENTS.md)
        //   .agent/skills/<name>/  → skills/workflows as native slash commands (note: .agent singular)
        $agentsMdPath = base_path('AGENTS.md');
        $skillsTarget = '.agent/skills';

        // 1. Compile all rules → AGENTS.md (cross-tool standard, auto-loaded)
        $this->smartUpdate($agentsMdPath, $this->compileToSingleFile());

        // 2. Convert each workflow → a real Antigravity skill (.agent/skills/<name>/SKILL.md)
        foreach ($this->files->files($this->sourcePath() . '/workflows') as $file) {
            $skillName = $file->getFilenameWithoutExtension();
            $skillDir  = base_path("{$skillsTarget}/{$skillName}");

            $this->files->ensureDirectoryExists($skillDir);
            $this->files->put(
                $skillDir . '/SKILL.md',
                $this->buildAgentSkill($skillName, $file->getContents())
            );
        }

        // 3. Knowledge template → .agent/knowledge/ (separate from skills)
        $this->files->copyDirectory(
            $this->sourcePath() . '/knowledge',
            base_path('.agent/knowledge')
        );

        $this->success("AGENTS.md + {$skillsTarget}/", [
            'Project rules compiled to <comment>AGENTS.md</comment> (auto-loaded by Antigravity v1.20.3+)',
            'Workflows available as native skills: <comment>/code-review</comment>, <comment>/investigate</comment>, etc.',
            'Fill in your primary key type in the <comment>Project Defaults</comment> section of <comment>AGENTS.md</comment>',
            'Knowledge template at <comment>.agent/knowledge/_TEMPLATE.md</comment>',
            'Tip: create <comment>GEMINI.md</comment> for Antigravity-specific overrides (higher priority than AGENTS.md)',
        ]);

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function copyAsMdc(string $sourcePath, string $targetPath): void
    {
        foreach ($this->files->allFiles($sourcePath) as $file) {
            $nameWithoutExt = $file->getFilenameWithoutExtension();
            $relativePath   = $file->getRelativePathname();
            $destRelative   = preg_replace('/\.md$/', '.mdc', $relativePath);
            $destPath       = $targetPath . DIRECTORY_SEPARATOR . $destRelative;
            $isRule         = str_starts_with($file->getRelativePath(), 'rules');

            $this->files->ensureDirectoryExists(dirname($destPath));

            $description = ucwords(str_replace(['-', '_'], ' ', $nameWithoutExt));

            if (in_array($nameWithoutExt, self::CURSOR_ALWAYS_APPLY, true)) {
                // Always injected — core rules every session needs
                $frontmatter = "---\ndescription: Laravel API Toolkit - {$description}\nalwaysApply: true\n---\n\n";
            } elseif ($isRule) {
                // Auto Attached — triggers automatically when PHP files are open
                $frontmatter = "---\ndescription: Laravel API Toolkit - {$description}\nalwaysApply: false\nglobs: [\"**/*.php\"]\n---\n\n";
            } else {
                // Workflows & knowledge — Manual, invoked explicitly with @name
                $frontmatter = "---\ndescription: Laravel API Toolkit - {$description}\nalwaysApply: false\n---\n\n";
            }

            $this->files->put($destPath, $frontmatter . $file->getContents());
        }
    }

    private function compileToSingleFile(): string
    {
        $sourcePath = $this->sourcePath();
        $sections   = [];

        // SKILL.md first (strip YAML front matter)
        $skillContent = $this->files->get($sourcePath . '/SKILL.md');
        $skillContent = preg_replace('/^---\n.*?---\n\n?/s', '', $skillContent);
        $sections[]   = trim($skillContent);

        // All rule files
        foreach ($this->files->files($sourcePath . '/rules') as $file) {
            $sections[] = trim($file->getContents());
        }

        // All workflow files
        foreach ($this->files->files($sourcePath . '/workflows') as $file) {
            $sections[] = trim($file->getContents());
        }

        return implode("\n\n---\n\n", $sections) . "\n";
    }

    private function compileCopilotGlobal(): string
    {
        $sourcePath = $this->sourcePath();
        $sections   = [];

        // SKILL.md overview (strip YAML front matter)
        $skillContent = $this->files->get($sourcePath . '/SKILL.md');
        $skillContent = preg_replace('/^---\n.*?---\n\n?/s', '', $skillContent);
        $sections[]   = trim($skillContent);

        // Core always-on rules only
        foreach ($this->files->files($sourcePath . '/rules') as $file) {
            if (in_array($file->getFilenameWithoutExtension(), self::CURSOR_ALWAYS_APPLY, true)) {
                $sections[] = trim($file->getContents());
            }
        }

        return implode("\n\n---\n\n", $sections) . "\n";
    }

    private function compileCopilotPhpInstructions(): string
    {
        $sourcePath = $this->sourcePath();
        $sections   = [];

        // PHP-specific rules (all except the always-on ones already in global)
        foreach ($this->files->files($sourcePath . '/rules') as $file) {
            if ( ! in_array($file->getFilenameWithoutExtension(), self::CURSOR_ALWAYS_APPLY, true)) {
                $sections[] = trim($file->getContents());
            }
        }

        $body = implode("\n\n---\n\n", $sections);

        return "---\napplyTo: \"**/*.php\"\n---\n\n{$body}\n";
    }

    private function buildClaudeSkill(string $name, string $content): string
    {
        $description = $this->extractSkillDescription($content);

        return "---\nname: {$name}\ndescription: {$description}\nuser-invocable: true\n---\n\n{$content}";
    }

    private function buildAgentSkill(string $name, string $content): string
    {
        $description = $this->extractSkillDescription($content);

        return "---\nname: {$name}\ndescription: {$description}\n---\n\n{$content}";
    }

    private function extractSkillDescription(string $content): string
    {
        $lines      = explode("\n", $content);
        $seenH1     = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, '# ')) {
                $seenH1 = true;
                continue;
            }

            if ($seenH1 && ! empty($trimmed) && ! str_starts_with($trimmed, '#') && ! str_starts_with($trimmed, '**Trigger')) {
                return $trimmed;
            }
        }

        // Fallback: use H1 title
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '# ')) {
                return ltrim(mb_substr($trimmed, 2));
            }
        }

        return ucwords(str_replace(['-', '_'], ' ', basename($content)));
    }

    private function smartUpdate(string $path, string $newContent): void
    {
        if ( ! $this->files->exists($path)) {
            $this->files->put($path, $this->wrapInMarkers($newContent));

            return;
        }

        $existingContent = $this->files->get($path);
        $startMarker     = '<!-- LARAVEL API TOOL KIT START -->';
        $endMarker       = '<!-- LARAVEL API TOOL KIT END -->';

        $pattern = "/{$startMarker}.*?{$endMarker}/s";
        $wrapped = $this->wrapInMarkers($newContent);

        if (preg_match($pattern, $existingContent)) {
            $updatedContent = preg_replace($pattern, $wrapped, $existingContent);
        } else {
            $updatedContent = $existingContent . "\n\n" . $wrapped;
        }

        $this->files->put($path, $updatedContent);
    }

    private function wrapInMarkers(string $content): string
    {
        return "<!-- LARAVEL API TOOL KIT START -->\n"
            . trim($content)
            . "\n<!-- LARAVEL API TOOL KIT END -->";
    }

    private function appendToClaudeMd(): void
    {
        $path = base_path('CLAUDE.md');

        if ( ! $this->files->exists($path)) {
            return;
        }

        if (str_contains($this->files->get($path), '.claude/rules/laravel-api-tool-kit')) {
            return;
        }

        $this->files->append(
            $path,
            "\n\n## Laravel API Tool Kit\n\nRules are in `.claude/rules/laravel-api-tool-kit/` (auto-loaded). Workflows are available as slash commands: `/code-review`, `/investigate`, `/new-endpoint`, etc.\n"
        );

        $this->line('  <comment>→</comment> Added reference to <comment>CLAUDE.md</comment>');
    }

    private function shouldProceed(string $target): bool
    {
        if ( ! $this->files->isDirectory(base_path($target))) {
            return true;
        }

        if ($this->option('force')) {
            return true;
        }

        if ( ! $this->confirm("  Directory [<comment>{$target}</comment>] already exists. Overwrite?")) {
            $this->line('  Installation cancelled.');

            return false;
        }

        return true;
    }

    private function success(string $target, array $nextSteps): void
    {
        $this->line("  <info>✓</info> Installed to <comment>{$target}</comment>");
        $this->newLine();
        $this->line('  <comment>Next steps:</comment>');

        foreach ($nextSteps as $step) {
            $this->line("  • {$step}");
        }

        $this->newLine();
    }

    private function sourcePath(): string
    {
        return __DIR__ . '/../../skills/laravel-api-tool-kit';
    }
}
