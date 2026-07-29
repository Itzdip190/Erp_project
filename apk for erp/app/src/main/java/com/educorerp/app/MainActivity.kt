package com.educorerp.app

import android.annotation.SuppressLint
import android.graphics.Bitmap
import android.os.Bundle
import android.os.Handler
import android.os.Looper
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
import androidx.appcompat.app.AppCompatActivity

/**
 * Android Native Presentation Layer
 * 
 * ARCHITECTURE DIRECTIVE:
 * This Android application operates as an independent presentation layer consuming shared backend APIs.
 * It does NOT depend on Web ERP Blade views. Any UI changes made here are strictly isolated to Mobile.
 */
class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var splashWebView: WebView
    private val targetUrl = "https://www.educorerp.com/"

    private var isPageLoaded = false
    private var isSplashDismissed = false
    private val mainHandler = Handler(Looper.getMainLooper())

    @SuppressLint("SetJavaScriptEnabled", "JavascriptInterface")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        try {
            webView = findViewById(R.id.webView)
            splashWebView = findViewById(R.id.splashWebView)

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

            // Enable Fast Disk Caching & High Render Priority
            settings.cacheMode = WebSettings.LOAD_DEFAULT
            @Suppress("DEPRECATION")
            settings.setRenderPriority(WebSettings.RenderPriority.HIGH)
            settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW

            // Enable Cookies for persistent sessions
            CookieManager.getInstance().setAcceptCookie(true)
            CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true)

            // Enable GPU Hardware Acceleration for 60 FPS Scrolling
            webView.setLayerType(View.LAYER_TYPE_HARDWARE, null)

            // WebView Client handling page load completion
            webView.webViewClient = object : WebViewClient() {
                override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                    super.onPageStarted(view, url, favicon)
                }

                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    isPageLoaded = true
                }

                override fun onReceivedError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    error: WebResourceError?
                ) {
                    super.onReceivedError(view, request, error)
                    isPageLoaded = true
                }
            }

            // Absolute Safety Timeout (4.5 seconds max) to guarantee splash dismissal
            mainHandler.postDelayed({
                dismissSplashWithAnimation()
            }, 4500)

            // Load Target URL in background
            webView.loadUrl(targetUrl)

        } catch (e: Exception) {
            e.printStackTrace()
            dismissSplashImmediately()
        }
    }

    // JavaScript Interface to receive trigger from HTML animation
    inner class AndroidBridge {
        @JavascriptInterface
        fun onSplashComplete() {
            mainHandler.post {
                dismissSplashWithAnimation()
            }
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
