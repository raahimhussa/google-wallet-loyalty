// MainActivity.kt
class MainActivity : AppCompatActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        // Initialize Firebase
        FirebaseApp.initializeApp(this)
        
        // Get FCM token
        FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
            if (!task.isSuccessful) {
                Log.w("FCM", "Fetching FCM registration token failed", task.exception)
                return@addOnCompleteListener
            }
            
            val token = task.result
            Log.d("FCM", "FCM Registration Token: $token")
            
            // Send token to your server
            sendTokenToServer(token)
        }
    }
    
    private fun sendTokenToServer(token: String) {
        // Send token to your Laravel backend
        // This token will be used for push notifications
    }
}