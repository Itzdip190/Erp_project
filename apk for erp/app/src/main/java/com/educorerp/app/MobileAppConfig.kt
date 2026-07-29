package com.educorerp.app

/**
 * Mobile App Presentation Configuration & API Data Models
 * Decoupled from Web ERP Blade presentation layer.
 */
data class MobileAppTheme(
    val primaryColor: String = "#1e293b",
    val accentColor: String = "#3b82f6",
    val backgroundColor: String = "#f8fafc",
    val cardBackground: String = "#ffffff",
    val textColor: String = "#0f172a"
)

data class MobileNavigationItem(
    val key: String,
    val label: String,
    val icon: String,
    val route: String
)

data class MobileDashboardCard(
    val key: String,
    val title: String,
    val icon: String,
    val color: String
)

data class MobileFeatureVisibility(
    val features: Map<String, Boolean> = emptyMap()
)
