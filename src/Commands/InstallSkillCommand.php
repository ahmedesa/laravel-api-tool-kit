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

    // Rules that should always be active in Cursor (not just when referenced)
    private const CURSOR_ALWAYS_APPLY = [
        'SKILL',
        'anti-patterns',
        'code-quality',
    ];

    protected $signature = 'api-skill:install
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

        $tool = $this->choice('Which AI coding tool are you using?', self::TOOLS);

        $this->newLine();

        return match ($tool) {
            'Claude Code'    => $this->installForClaude(),
            'Cursor'         => $this->installForCursor(),
            'GitHub Copilot' => $this->installForCopilot(),
            'Antigravity'    => $this->installForAntigravity(),
            default          => self::FAILURE,
        };
    }

    // -------------------------------------------------------------------------
    // Tool-specific installers
    // -------------------------------------------------------------------------

    private function installForClaude(): int
    {
        $target = '.claude/skills/laravel-api';

        if ( ! $this->shouldProceed($target)) {
            return self::SUCCESS;
        }

        $this->files->copyDirectory($this->sourcePath(), base_path($target));
        $this->appendToClaudeMd();

        $this->success($target, [
            'Fill in your primary key type in <comment>.claude/skills/laravel-api/SKILL.md</comment> → Project Defaults',
            'Claude Code loads the skill automatically when the .claude/skills/ directory is present',
        ]);

        return self::SUCCESS;
    }

    private function installForCursor(): int
    {
        // Cursor reads .cursor/rules/**/*.mdc
        // Rules without alwaysApply are "Manual" (only loaded when referenced with @ruleName)
        // Rules with alwaysApply: true are injected in every request
        $target = '.cursor/rules/laravel-api';

        if ( ! $this->shouldProceed($target)) {
            return self::SUCCESS;
        }

        $targetPath = base_path($target);
        $this->files->ensureDirectoryExists($targetPath);
        $this->copyAsMdc($this->sourcePath(), $targetPath);

        $this->success($target, [
            'Core rules (SKILL, anti-patterns, code-quality) are set to <comment>alwaysApply: true</comment>',
            'All other rules load on demand — reference them with <comment>@ruleName</comment> in Cursor chat',
            'Fill in your primary key type in <comment>.cursor/rules/laravel-api/SKILL.mdc</comment> → Project Defaults',
        ]);

        return self::SUCCESS;
    }

    private function installForCopilot(): int
    {
        $target = '.github/copilot-instructions.md';

        $this->files->ensureDirectoryExists(base_path('.github'));

        $this->smartUpdate(base_path($target), $this->compileToSingleFile());

        $this->success($target, [
            'Copilot will automatically load this file as project instructions',
            'Fill in your primary key type in the <comment>Project Defaults</comment> section',
        ]);

        return self::SUCCESS;
    }

    private function installForAntigravity(): int
    {
        $instructionsTarget = '.agents/instructions.md';
        $workflowsTarget    = '.agents/workflows';

        $this->files->ensureDirectoryExists(base_path('.agents'));

        $this->smartUpdate(base_path($instructionsTarget), $this->compileToSingleFile());

        $this->files->copyDirectory(
            $this->sourcePath() . '/workflows',
            base_path($workflowsTarget)
        );

        $this->success('.agents/', [
            'Project rules updated in <comment>.agents/instructions.md</comment>',
            'Workflows copied to <comment>.agents/workflows/</comment>',
            'Fill in your primary key type in the <comment>Project Defaults</comment> section',
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

            $this->files->ensureDirectoryExists(dirname($destPath));

            $alwaysApply = in_array($nameWithoutExt, self::CURSOR_ALWAYS_APPLY, true) ? 'true' : 'false';
            $description = ucwords(str_replace(['-', '_'], ' ', $nameWithoutExt));

            $content = "---\ndescription: Laravel API Toolkit - {$description}\nalwaysApply: {$alwaysApply}\n---\n\n"
                . $file->getContents();

            $this->files->put($destPath, $content);
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

        if (str_contains($this->files->get($path), '.claude/skills/laravel-api')) {
            return;
        }

        $this->files->append(
            $path,
            "\n\n## Laravel API Tool Kit Skill\n\nFollow the rules and patterns defined in `.claude/skills/laravel-api/SKILL.md` for all API code.\n"
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
        return __DIR__ . '/../../skill/laravel-api';
    }
}
