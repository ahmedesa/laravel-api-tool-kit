<?php

namespace Essa\APIToolKit\Tests;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

class InstallSkillCommandTest extends TestCase
{
    private string $tempPath;

    public function setUp(): void
    {
        parent::setUp();

        // The orchestratestbench base_path is usually in /tmp or a temp vendor folder
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
            ->expectsOutput('  ✓ Installed to .cursor/rules/laravel-api')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.cursor/rules/laravel-api/SKILL.mdc'));
        $this->assertFileExists(base_path('.cursor/rules/laravel-api/rules/actions.mdc'));

        $content = File::get(base_path('.cursor/rules/laravel-api/SKILL.mdc'));
        $this->assertStringContainsString('alwaysApply: true', $content);
        $this->assertStringContainsString('description: Laravel API Toolkit - SKILL', $content);

        $actionContent = File::get(base_path('.cursor/rules/laravel-api/rules/actions.mdc'));
        $this->assertStringContainsString('alwaysApply: false', $actionContent);
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
            ->expectsOutput('  ✓ Installed to .claude/skills/laravel-api')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.claude/skills/laravel-api/SKILL.md'));
        $this->assertStringContainsString('.claude/skills/laravel-api/SKILL.md', File::get(base_path('CLAUDE.md')));
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
            ->expectsOutput('  ✓ Installed to .agents/')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.agents/instructions.md'));
        $this->assertDirectoryExists(base_path('.agents/workflows'));
        $this->assertFileExists(base_path('.agents/workflows/new-endpoint.md'));

        $content = File::get(base_path('.agents/instructions.md'));
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $content);
        $this->assertStringContainsString('# Action Classes', $content);
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT END -->', $content);
    }

    /** @test */
    public function it_updates_existing_rules_non_destructively(): void
    {
        File::ensureDirectoryExists(base_path('.agents'));
        File::put(base_path('.agents/instructions.md'), "# User Rules\nMy custom rule.");

        $this->artisan('api-skill:install')
            ->expectsChoice('Which AI coding tool are you using?', 'Antigravity', [
                'Claude Code',
                'Cursor',
                'GitHub Copilot',
                'Antigravity',
            ])
            ->assertExitCode(Command::SUCCESS);

        $content = File::get(base_path('.agents/instructions.md'));
        $this->assertStringContainsString("# User Rules\nMy custom rule.", $content);
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $content);
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
            ->expectsOutput('  ✓ Installed to .github/copilot-instructions.md')
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists(base_path('.github/copilot-instructions.md'));

        $content = File::get(base_path('.github/copilot-instructions.md'));
        $this->assertStringContainsString('<!-- LARAVEL API TOOL KIT START -->', $content);
        $this->assertStringContainsString('# Action Classes', $content);
    }

    private function cleanup(): void
    {
        File::deleteDirectory(base_path('.cursor'));
        File::deleteDirectory(base_path('.agents'));
        File::deleteDirectory(base_path('.github'));
        File::deleteDirectory(base_path('.claude'));
        File::deleteDirectory(base_path('skill'));
        File::delete(base_path('CLAUDE.md'));
    }
}
