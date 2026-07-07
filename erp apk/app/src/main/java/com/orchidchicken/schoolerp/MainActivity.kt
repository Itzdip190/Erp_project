package com.orchidchicken.schoolerp

import android.os.Bundle
import android.view.ViewGroup
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.ComponentActivity
import androidx.activity.compose.BackHandler
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.ui.Modifier
import androidx.compose.ui.viewinterop.AndroidView

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            WebViewScreen(url = "https://orchid-chicken-193400.hostingersite.com/")
        }
    }
}

@androidx.compose.runtime.Composable
fun WebViewScreen(url: String) {
    var webView: WebView? = null

    // This handles the device's hardware back button (goes back in web pages)
    BackHandler(enabled = true) {
        if (webView?.canGoBack() == true) {
            webView?.goBack()
        } else {
            // Closes the application if there is no back history left
            System.exit(0)
        }
    }

    AndroidView(
        modifier = Modifier.fillMaxSize(),
        factory = { context ->
            WebView(context).apply {
                layoutParams = ViewGroup.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT,
                    ViewGroup.LayoutParams.MATCH_PARENT
                )
                webViewClient = WebViewClient()

                // Settings to make modern responsive websites work
                settings.javaScriptEnabled = true
                settings.domStorageEnabled = true // Allows local storage (like session/login tokens)
                settings.loadWithOverviewMode = true
                settings.useWideViewPort = true

                loadUrl(url)
                webView = this
            }
        },
        update = {
            webView = it
        }
    )
}
