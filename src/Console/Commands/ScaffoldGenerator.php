<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Filesystem\Filesystem;

/**
 * ScaffoldGenerator
 *
 * A single-responsibility class for stub-based file scaffolding.
 * Handles: copy template → replace placeholders → write to target.
 *
 * Used by CmsModuleControllerCommand, CmsModuleModelCommand, etc.
 */
class ScaffoldGenerator
{
    protected Filesystem $files;

    /** Absolute path to the package root (the directory containing /src and /hashtagcms) */
    protected string $packageBasePath;

    public function __construct(Filesystem $files, string $packageBasePath)
    {
        $this->files = $files;
        $this->packageBasePath = rtrim($packageBasePath, '/');
    }

    /**
     * Resolve a stub path relative to the package root.
     *
     * Example: getStubPath('hashtagcms/cmsmodule/controller/index.ms')
     */
    public function getStubPath(string $relativePath): string
    {
        return $this->packageBasePath . '/' . ltrim($relativePath, '/');
    }

    /**
     * Read a stub file, replace all {{placeholder}} tokens with values,
     * and write the result to the target destination.
     *
     * @param string $stubPath    Absolute path to the .ms stub file
     * @param string $targetPath  Absolute path to write the generated file
     * @param array  $replacements  [ '{{token}}' => 'value', ... ] or [ 'token' => 'value', ... ]
     * @param bool   $overwrite   Whether to overwrite if target already exists
     * @return bool  true if written, false if skipped (already exists and not overwriting)
     */
    public function generate(string $stubPath, string $targetPath, array $replacements, bool $overwrite = false): bool
    {
        if (!$overwrite && $this->files->exists($targetPath)) {
            return false;
        }

        $stub = $this->files->get($stubPath);

        $stub = $this->replaceTokens($stub, $replacements);

        $this->files->ensureDirectoryExists(dirname($targetPath));
        $this->files->put($targetPath, $stub);

        return true;
    }

    /**
     * Replace {{token}} placeholders in content.
     * Accepts keys with or without {{ }} braces.
     */
    public function replaceTokens(string $content, array $replacements): string
    {
        $patterns = [];
        $values   = [];

        foreach ($replacements as $key => $value) {
            // Normalise: strip braces if caller included them
            $token = trim($key, '{}');
            $patterns[] = '/\{\{' . preg_quote($token, '/') . '\}\}/';
            $values[]   = $value ?? '';
        }

        return preg_replace($patterns, $values, $content);
    }

    /**
     * Ensure a directory exists (creates recursively if needed).
     */
    public function ensureDirectory(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }
}
