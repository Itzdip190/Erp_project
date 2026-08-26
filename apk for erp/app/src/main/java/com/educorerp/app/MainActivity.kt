package com.educorerp.app

import android.Manifest
import android.annotation.SuppressLint
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.util.Log
import android.view.KeyEvent
import android.view.View
import android.view.animation.Animation
import android.view.animation.AnimationUtils
import android.webkit.CookieManager
import android.webkit.JavascriptInterface
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import com.google.firebase.messaging.FirebaseMessaging
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

/**
 * Android Native Presentation Layer
 * 
 * ARCHITECTURE DIRECTIVE:
 * This Android application operates as an independent presentation layer consuming shared backend APIs.
 * It integrates native Firebase Cloud Messaging for lock screen alerts and ringtones.
 */
class MainActivity : AppCompatActivity() {

    companion object {
        const val TAG = "EduCorMainActivity"
    }

    private lateinit var webView: WebView
    private lateinit var splashWebView: WebView
    private val targetUrl = "https://www.educorerp.com/"

    private var isPageLoaded = false
    private var isSplashDismissed = false
    private val mainHandler = Handler(Looper.getMainLooper())

    // Android 13+ Notification Permission Launcher
    private val requestNotificationPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted: Boolean ->
        if (isGranted) {
            Log.d(TAG, "POST_NOTIFICATIONS permission granted by user.")
            initFirebaseToken()
        } else {
            Log.w(TAG, "POST_NOTIFICATIONS permission denied by user.")
        }
    }

    @SuppressLint("SetJavaScriptEnabled", "JavascriptInterface")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        try {
            webView = findViewById(R.id.webView)
            splashWebView = findViewById(R.id.splashWebView)

            // Request Notification Permission on Android 13+ (API 33+)
            checkAndRequestNotificationPermission()

            // Initialize FCM Token & Sync with Server
            initFirebaseToken()

            // Handle Deep link from push notification intent
            handleNotificationIntent(intent)

            // Set white background to prevent any black screen flash during load
            webView.setBackgroundColor(android.graphics.Color.WHITE)
            splashWebView.setBackgroundColor(android.graphics.Color.WHITE)

            // 1. Configure Splash WebView (Plays HTML5/CSS Keyframe Animation)
            val splashSettings: WebSettings = splashWebView.settings
            splashSettings.javaScriptEnabled = true
            splashSettings.domStorageEnabled = true
            splashSettings.allowFileAccess = true

            splashWebView.setLayerType(View.LAYER_TYPE_HARDWARE, null)
            splashWebView.addJavascriptInterface(AndroidBridge(), "AndroidBridge")
            splashWebView.loadUrl("file:///android_asset/splash.html")

            // 2. Maximum Performance Configuration for Main WebView
            val settings: WebSettings = webView.settings
            settings.javaScriptEnabled = true
            settings.domStorageEnabled = true
            settings.databaseEnabled = true
            settings.allowFileAccess = true
            settings.useWideViewPort = true
            settings.loadWithOverviewMode = true
            settings.builtInZoomControls = false
            settings.displayZoomControls = false
            settings.setNeedInitialFocus(false)
            settings.mediaPlaybackRequiresUserGesture = false
            settings.loadsImagesAutomatically = true

            // Enable Fast Disk Caching & High Render Priority
            settings.cacheMode = WebSettings.LOAD_DEFAULT
            @Suppress("DEPRECATION")
            settings.setRenderPriority(WebSettings.RenderPriority.HIGH)
            settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW

            // Enable Cookies for persistent sessions
            CookieManager.getInstance().setAcceptCookie(true)
            CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true)

            // Enable GPU Hardware Acceleration & Smooth 60 FPS Scroll Optimization
            webView.setLayerType(View.LAYER_TYPE_HARDWARE, null)
            webView.overScrollMode = View.OVER_SCROLL_NEVER
            webView.isScrollbarFadingEnabled = true
            webView.isHapticFeedbackEnabled = false

            // WebView Client handling page load completion
            webView.webViewClient = object : WebViewClient() {
                override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                    super.onPageStarted(view, url, favicon)
                }

                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    isPageLoaded = true
                    dismissSplashWithAnimation()
                }

                override fun onReceivedError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    error: WebResourceError?
                ) {
                    super.onReceivedError(view, request, error)
                    isPageLoaded = true
                    dismissSplashWithAnimation()
                }
            }

            // Absolute Safety Timeout (3.5 seconds max) to guarantee splash dismissal
            mainHandler.postDelayed({
                dismissSplashWithAnimation()
            }, 3500)

            // Load Target URL in background
            webView.loadUrl(targetUrl)

        } catch (e: Exception) {
            e.printStackTrace()
            dismissSplashImmediately()
        }
    }

    private fun checkAndRequestNotificationPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            val permissionCheck = ContextCompat.checkSelfPermission(
                this,
                Manifest.permission.POST_NOTIFICATIONS
            )
            if (permissionCheck != PackageManager.PERMISSION_GRANTED) {
                requestNotificationPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }
    }

    private fun initFirebaseToken() {
        try {
            FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
                if (!task.isSuccessful) {
                    Log.w(TAG, "Fetching FCM registration token failed", task.exception)
                    return@addOnCompleteListener
                }

                val token = task.result
                Log.d(TAG, "Fetched FCM Device Token: $token")
                saveTokenLocally(token)
                syncTokenWithServer(token)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error initializing FCM: ${e.message}")
        }
    }

    private fun saveTokenLocally(token: String) {
        val prefs = getSharedPreferences("educor_app_prefs", Context.MODE_PRIVATE)
        prefs.edit().putString("fcm_device_token", token).apply()
    }

    private fun syncTokenWithServer(token: String) {
        Thread {
            try {
                val url = URL("https://www.educorerp.com/notifications/register-device")
                val conn = url.openConnection() as HttpURLConnection
                conn.requestMethod = "POST"
                conn.setRequestProperty("Content-Type", "application/json; utf-8")
                conn.setRequestProperty("Accept", "application/json")
                conn.doOutput = true
                conn.connectTimeout = 8000
                conn.readTimeout = 8000

                val json = JSONObject().apply {
                    put("token", token)
                    put("platform", "android")
                    put("device_name", "${Build.MANUFACTURER} ${Build.MODEL}")
                }

                OutputStreamWriter(conn.outputStream).use { writer ->
                    writer.write(json.toString())
                    writer.flush()
                }

                val responseCode = conn.responseCode
                Log.d(TAG, "FCM token synced with server. HTTP Status: $responseCode")
                conn.disconnect()
            } catch (e: Exception) {
                Log.e(TAG, "Error syncing FCM token: ${e.message}")
            }
        }.start()
    }

    private fun handleNotificationIntent(intent: Intent?) {
        val actionUrl = intent?.getStringExtra("action_url")
        if (!actionUrl.isNullOrEmpty() && actionUrl != "/") {
            val destination = if (actionUrl.startsWith("http")) actionUrl else targetUrl.trimEnd('/') + actionUrl
            mainHandler.postDelayed({
                if (::webView.isInitialized) {
                    webView.loadUrl(destination)
                }
            }, 1000)
        }
    }

    override fun onNewIntent(intent: Intent?) {
        super.onNewIntent(intent)
        setIntent(intent)
        handleNotificationIntent(intent)
    }

    // JavaScript Interface to receive trigger from HTML animation
    inner class AndroidBridge {
        @JavascriptInterface
        fun onSplashComplete() {
            mainHandler.post {
                dismissSplashWithAnimation()
            }
        }

        @JavascriptInterface
        fun getFcmToken(): String {
            val prefs = getSharedPreferences("educor_app_prefs", Context.MODE_PRIVATE)
            return prefs.getString("fcm_device_token", "") ?: ""
        }
    }

    private fun dismissSplashWithAnimation() {
        if (isSplashDismissed) return
        isSplashDismissed = true

        try {
            if (::splashWebView.isInitialized && splashWebView.visibility == View.VISIBLE) {
                val fadeOut = AnimationUtils.loadAnimation(this, android.R.anim.fade_out)
                fadeOut.duration = 600
                fadeOut.setAnimationListener(object : Animation.AnimationListener {
                    override fun onAnimationStart(animation: Animation?) {}
                    override fun onAnimationRepeat(animation: Animation?) {}
                    override fun onAnimationEnd(animation: Animation?) {
                        splashWebView.visibility = View.GONE
                        splashWebView.destroy()
                    }
                })
                splashWebView.startAnimation(fadeOut)
            }
        } catch (e: Exception) {
            dismissSplashImmediately()
        }
    }

    private fun dismissSplashImmediately() {
        try {
            if (::splashWebView.isInitialized && splashWebView.visibility == View.VISIBLE) {
                splashWebView.visibility = View.GONE
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    override fun onKeyDown(keyCode: Int, event: KeyEvent?): Boolean {
        if (keyCode == KeyEvent.KEYCODE_BACK && ::webView.isInitialized && webView.canGoBack()) {
            webView.goBack()
            return true
        }
        return super.onKeyDown(keyCode, event)
    }

    override fun onDestroy() {
        mainHandler.removeCallbacksAndMessages(null)
        super.onDestroy()
    }
}
