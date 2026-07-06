<?php

namespace App\Support;

/**
 * Central registry of all ERP modules and their features.
 * Add a new entry here and it will automatically appear in Role Category
 * and Staff Access Control pages without any further changes.
 */
class ModuleRegistry
{
    /**
     * Returns the full module list.
     * Format: [ 'module_key' => [ 'label' => '...', 'icon' => 'fa-...', 'features' => [ 'feature_key' => 'Feature Label' ] ] ]
     */
    public static function all(): array
    {
        $sidebarPath = resource_path('views/layouts/sidebar_nav.blade.php');
        if (!file_exists($sidebarPath)) {
            return [];
        }

        $content = file_get_contents($sidebarPath);
        $modules = [];

        // Parse using regex matching each main module block: @if(StaffAccessHelper::hasAccess('module_name'))
        $pattern = '/@if\(StaffAccessHelper::hasAccess\(\s*[\'"]([a-zA-Z0-9_-]+)[\'"]\s*\)\)(.*?)(?=@if\(StaffAccessHelper::hasAccess\(\s*[\'"][a-zA-Z0-9_-]+[\'"]\s*\)\)|$)/s';

        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $moduleKey = $match[1];
                $blockContent = $match[2];

                // 1. Extract Label/Title
                $originalTitle = '';
                $label = '';
                if (preg_match('/class="sb-hdr-title"[^>]*>(.*?)<\/span>/s', $blockContent, $titleMatch)) {
                    $rawTitle = trim(strip_tags($titleMatch[1]));
                    if (preg_match('/getLabel\(\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $rawTitle, $labelMatch)) {
                        $originalTitle = $labelMatch[1];
                    } else {
                        $originalTitle = $rawTitle;
                    }
                    $originalTitle = preg_replace('/^\d+\.\s+/', '', $originalTitle);
                    $label = $originalTitle;
                }

                // 2. Extract Icon (supports fa-icon classes and img tags)
                $icon = '';
                if (preg_match('/class="sb-hdr-icon"[^>]*>(.*?)<\/div>/s', $blockContent, $iconDivMatch)) {
                    $divContent = $iconDivMatch[1];
                    if (preg_match('/<i\s+class="([^"]+)"/s', $divContent, $iMatch)) {
                        $icon = trim($iMatch[1]);
                    } elseif (preg_match('/<img[^>]+src="([^"]+)"/s', $divContent, $imgMatch)) {
                        $icon = trim($imgMatch[1]);
                    }
                }
                if (!$icon) {
                    if (preg_match('/<i\s+class="([^"]*fa-[^"]+)"/s', $blockContent, $iMatch)) {
                        $icon = trim($iMatch[1]);
                    }
                }

                // 3. Extract Features (Submenus)
                $features = [];

                // Match features checking access: @if(StaffAccessHelper::hasAccess('module_key', 'feature_key'))
                $featPattern = '/@if\(StaffAccessHelper::hasAccess\(\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]([a-zA-Z0-9_-]+)[\'"]\s*\)\)(.*?)(?=@if\(StaffAccessHelper::hasAccess|@endif|$)/s';
                if (preg_match_all($featPattern, $blockContent, $featMatches, PREG_SET_ORDER)) {
                    foreach ($featMatches as $fMatch) {
                        $fKey = $fMatch[1];
                        $fBlock = $fMatch[2];
                        if (preg_match('/class="sb-submenu-label"[^>]*>(.*?)<\/span>/s', $fBlock, $lblMatch)) {
                            $rawLbl = trim(strip_tags($lblMatch[1]));
                            if (preg_match('/getFeatureLabel\(\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $rawLbl, $featLblMatch)) {
                                $featLabel = $featLblMatch[1];
                            } else {
                                $featLabel = $rawLbl;
                            }
                            $features[$fKey] = $featLabel;
                        }
                    }
                }

                // Fallback: If no hasAccess-based features found, parse all submenu labels directly
                if (empty($features)) {
                    if (preg_match('/<ul class="sb-submenu"[^>]*>(.*?)<\/ul>/s', $blockContent, $submenuMatch)) {
                        $submenuContent = $submenuMatch[1];
                        if (preg_match_all('/class="sb-submenu-label"[^>]*>(.*?)<\/span>/s', $submenuContent, $lblMatches)) {
                            foreach ($lblMatches[1] as $lbl) {
                                $rawLbl = trim(strip_tags($lbl));
                                if (preg_match('/getFeatureLabel\(\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $rawLbl, $featLblMatch)) {
                                    $featLabel = $featLblMatch[1];
                                } else {
                                    $featLabel = $rawLbl;
                                }
                                if ($featLabel !== '') {
                                    $featKey = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $featLabel));
                                    $featKey = trim($featKey, '_');
                                    $features[$featKey] = $featLabel;
                                }
                            }
                        }
                    }
                }

                $customNames = self::getCustomNames();
                $customName = $customNames[$moduleKey] ?? null;

                // Update feature names inside array with customized values if set
                foreach ($features as $fKey => $fLabel) {
                    $customFeatName = $customNames["{$moduleKey}:{$fKey}"] ?? null;
                    if ($customFeatName) {
                        $features[$fKey] = $customFeatName;
                    }
                }

                $modules[$moduleKey] = [
                    'label'          => $customName ?? $label,
                    'original_title' => $customName ?? $originalTitle,
                    'default_label'  => $label,
                    'default_title'  => $originalTitle,
                    'icon'           => $icon,
                    'features'       => $features,
                ];
            }
        }

        return $modules;
    }

    public static function getLabel(string $moduleKey, string $defaultLabel): string
    {
        $customNames = self::getCustomNames();
        $label = $customNames[$moduleKey] ?? $defaultLabel;
        return preg_replace('/^\d+[\.\s\x{00a0}]*/u', '', $label);
    }

    /**
     * Get customized label for a feature/page under a module.
     */
    public static function getFeatureLabel(string $moduleKey, string $featureKey, string $defaultLabel): string
    {
        $customNames = self::getCustomNames();
        $key = "{$moduleKey}:{$featureKey}";
        return $customNames[$key] ?? $defaultLabel;
    }

    /**
     * Get all custom menu names.
     */
    public static function getCustomNames(): array
    {
        $path = storage_path('app/menu_names.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?: [];
        }
        return [];
    }

    /**
     * Save custom menu names globally.
     */
    public static function saveCustomNames(array $names): void
    {
        $path = storage_path('app/menu_names.json');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($names, JSON_PRETTY_PRINT));
    }
}
