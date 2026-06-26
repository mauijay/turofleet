<?php

namespace App\Services\View;

class AssetManifestService
{
    /** Returns built Vite asset paths for the application shell. */
    public function appAssets(): array
    {
        $manifest = $this->manifest();

        return [
            'css' => $manifest['resources/css/app.css']['css'][0] ?? null,
            'js' => $manifest['resources/js/app.js']['file'] ?? null,
        ];
    }

    /** @return array<string, mixed> */
    private function manifest(): array
    {
        $manifestPath = FCPATH . 'build/.vite/manifest.json';

        if (! is_file($manifestPath)) {
            return [];
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        return is_array($manifest) ? $manifest : [];
    }
}
