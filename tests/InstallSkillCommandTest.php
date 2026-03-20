<?php

namespace Essa\APIToolKit\Tests;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

class InstallSkillCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->cleanup();
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }

    /** @test */
    public function it_installs_for_cursor(): void
    {
        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'Cursor', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->expectsOutput('  ✓ Installed to .cursor/rules/laravel-api-tool-kit')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.cursor/rules/laravel-api-tool-kit/SKILL.mdc'));
        $this->assertFileExists(base_path('.cursor/rules/laravel-api-tool-kit/rules/actions.mdc'));
        $this->assertFileExists(base_path('knowledge/_TEMPLATE.md'));

        $content = File::get(base_path('.cursor/rules/laravel-api-tool-kit/SKILL.mdc'));
        $this->assertStringContainsString('alwaysApply: true', $content);
        $this->assertStringContainsString('description: Laravel API Toolkit - SKILL', $content);

        $actionContent = File::get(base_path('.cursor/rules/laravel-api-tool-kit/rules/actions.mdc'));
        $this->assertStringContainsString('alwaysApply: false', $actionContent);
        $this->assertStringContainsString('globs: ["**/*.php"]', $actionContent);
    }

    /** @test */
    public function it_installs_for_claude_code(): void
    {
        File::put(base_path('CLAUDE.md'), "# Claude Instructions\n");

        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'Claude Code', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->expectsOutput('  ✓ Installed to .claude/rules/laravel-api-tool-kit + .claude/skills/')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.claude/rules/laravel-api-tool-kit/_overview.md'));
        $this->assertFileExists(base_path('.claude/rules/laravel-api-tool-kit/actions.md'));
        $this->assertFileExists(base_path('.claude/skills/code-review/SKILL.md'));
        $this->assertFileExists(base_path('.claude/knowledge/_TEMPLATE.md'));

        $claudemd = File::get(base_path('CLAUDE.md'));
        $this->assertStringContainsString('.claude/rules/laravel-api-tool-kit', $claudemd);
    }

    /** @test */
    public function it_installs_for_copilot(): void
    {
        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'GitHub Copilot', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->expectsOutput('  ✓ Installed to .github/')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.github/copilot-instructions.md'));
        $this->assertFileExists(base_path('.github/instructions/laravel-api-tool-kit.instructions.md'));
        $this->assertFileExists(base_path('knowledge/_TEMPLATE.md'));

        $global = File::get(base_path('.github/copilot-instructions.md'));
        $this->assertStringContainsString('Laravel API Tool Kit Skill', $global);
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $global);

        $phpSpecific = File::get(base_path('.github/instructions/laravel-api-tool-kit.instructions.md'));
        $this->assertStringContainsString('applyTo: "**/*.php"', $phpSpecific);
        $this->assertStringContainsString('# Action Classes', $phpSpecific);
    }

    /** @test */
    public function it_installs_for_antigravity(): void
    {
        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'Antigravity', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->expectsOutput('  ✓ Installed to AGENTS.md + .agent/skills/')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('AGENTS.md'));
        $this->assertFileExists(base_path('.agent/skills/code-review/SKILL.md'));
        $this->assertFileExists(base_path('.agent/knowledge/_TEMPLATE.md'));

        $content = File::get(base_path('AGENTS.md'));
        $this->assertStringContainsString('Laravel API Tool Kit Skill', $content);
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $content);
        $this->assertStringContainsString('# Action Classes', $content);
    }

    /** @test */
    public function it_performs_smart_update_on_antigravity(): void
    {
        File::put(base_path('AGENTS.md'), "Existing user rules.\n");

        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'Antigravity', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->assertExitCode(Command::SUCCESS);

        $content = File::get(base_path('AGENTS.md'));
        $this->assertStringContainsString("Existing user rules.\n", $content);
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $content);
    }

    private function cleanup(): void
    {
        File::deleteDirectory(base_path('.cursor'));
        File::deleteDirectory(base_path('.agent'));
        File::deleteDirectory(base_path('.github'));
        File::deleteDirectory(base_path('.claude'));
        File::deleteDirectory(base_path('knowledge'));
        File::deleteDirectory(base_path('skill'));
        File::delete(base_path('CLAUDE.md'));
        File::delete(base_path('AGENTS.md'));
        File::delete(base_path('GEMINI.md'));
    }
}
