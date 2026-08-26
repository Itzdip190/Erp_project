package com.educorerp.app

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.media.AudioAttributes
import android.media.RingtoneManager
import android.os.Build
import android.util.Log
import androidx.core.app.NotificationCompat
import androidx.core.app.NotificationManagerCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

/**
 * Native Firebase Cloud Messaging Service for EduCor ERP Mobile App
 * Delivers instant heads-up lock screen banners and ringtone chime sounds.
 */
class MyFirebaseMessagingService : FirebaseMessagingService() {

    companion object {
        const val TAG = "EduCorFCM"
        const val CHANNEL_ID = "school_erp_high_importance"
        const val CHANNEL_NAME = "School ERP Important Alerts"
    }

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d(TAG, "New FCM Registration Token: $token")
        saveTokenLocally(token)
        syncTokenWithServer(token)
    }

    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)
        Log.d(TAG, "From: ${remoteMessage.from}")

        val title = remoteMessage.notification?.title
            ?: remoteMessage.data["title"]
            ?: "School ERP Notification"

        val body = remoteMessage.notification?.body
            ?: remoteMessage.data["body"]
            ?: ""

        val actionUrl = remoteMessage.data["action_url"] ?: "/"

        showNativeNotification(title, body, actionUrl)
    }

    private fun showNativeNotification(title: String, body: String, actionUrl: String) {
        createNotificationChannel()

        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
            putExtra("action_url", actionUrl)
        }

        val pendingIntentFlag = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        } else {
            PendingIntent.FLAG_UPDATE_CURRENT
        }

        val pendingIntent = PendingIntent.getActivity(
            this,
            System.currentTimeMillis().toInt(),
            intent,
            pendingIntentFlag
        )

        val defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val vibrationPattern = longArrayOf(0, 250, 150, 250, 150, 300)

        val notificationBuilder = NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.mipmap.ic_launcher)
            .setContentTitle(title)
            .setContentText(body)
            .setStyle(NotificationCompat.BigTextStyle().bigText(body))
            .setAutoCancel(true)
            .setSound(defaultSoundUri)
            .setVibrate(vibrationPattern)
            .setPriority(NotificationCompat.PRIORITY_MAX)
            .setVisibility(NotificationCompat.VISIBILITY_PUBLIC) // Shows on Lock Screen
            .setCategory(NotificationCompat.CATEGORY_MESSAGE)
            .setContentIntent(pendingIntent)
            .setDefaults(NotificationCompat.DEFAULT_ALL)

        try {
            val notificationManager = NotificationManagerCompat.from(this)
            notificationManager.notify(System.currentTimeMillis().toInt(), notificationBuilder.build())
        } catch (e: SecurityException) {
            Log.e(TAG, "Notification permission missing on Android 13+: ${e.message}")
        } catch (e: Exception) {
            Log.e(TAG, "Error displaying notification: ${e.message}")
        }
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
            val audioAttributes = AudioAttributes.Builder()
                .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                .setUsage(AudioAttributes.USAGE_NOTIFICATION_EVENT)
                .build()

            val channel = NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Urgent alerts, homework diary, attendance and fee notifications from school"
                enableLights(true)
                lightColor = Color.BLUE
                enableVibration(true)
                vibrationPattern = longArrayOf(0, 250, 150, 250, 150, 300)
                setSound(defaultSoundUri, audioAttributes)
                lockscreenVisibility = android.app.Notification.VISIBILITY_PUBLIC
                setShowBadge(true)
            }

            val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            notificationManager.createNotificationChannel(channel)
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
                Log.d(TAG, "FCM token server sync HTTP Response: $responseCode")
                conn.disconnect()
            } catch (e: Exception) {
                Log.e(TAG, "Failed to auto-sync FCM token to server: ${e.message}")
            }
        }.start()
    }
}
