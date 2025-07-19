# Guide: Connecting to Live Google and Firebase Services

This document provides the steps to switch the application from using the local "fake" services to the real, live Google Wallet and Firebase Cloud Messaging services.

---

## **Prerequisites**

Before you begin, you must have completed the setup on Google's platforms:

1.  **Google Wallet Issuer Account:** You have successfully registered for a Google Wallet Issuer Account and have access to the [Google Pay & Wallet Console](https://pay.google.com/business/console/).
2.  **Google Cloud Service Account:** You have created a Service Account in the Google Cloud Console with the **"Wallet Object Writer"** role.
3.  **Firebase Project:** You have created a Firebase project and enabled the **Cloud Messaging API**.

---

## **Step 1: Configure Google Wallet Credentials**

You need two pieces of information from Google Cloud: your **Issuer ID** and your **Service Account Key**.

### 1A. Find Your Issuer ID

1.  Go to the [Google Pay & Wallet Console](https://pay.google.com/business/console/).
2.  Click on **"Google Wallet API"** on the left-hand menu.
3.  Your **Issuer ID** (a long number) will be displayed on this page. Copy it.

### 1B. Get Your Service Account Key

1.  Go to the [Google Cloud Console](https://console.cloud.google.com/).
2.  Navigate to **APIs & Services > Credentials**.
3.  Find the Service Account you created. Click on it.
4.  Go to the **"KEYS"** tab.
5.  Click **"ADD KEY"** > **"Create new key"**.
6.  Choose **JSON** and click **"CREATE"**. A JSON file will be downloaded.

### 1C. Update Your `.env` File

1.  Place the downloaded JSON key file in the `storage/app/` directory of your project and rename it to `google-wallet-service-account.json`.
2.  Open the `.env` file in the root of your `google-wallet-loyalty` project.
3.  Add or update the following lines, pasting your Issuer ID and the path to the key file:

```dotenv
GOOGLE_WALLET_ISSUER_ID=YOUR_ISSUER_ID_HERE
GOOGLE_WALLET_CREDENTIALS_PATH=storage/app/google-wallet-service-account.json
```

---

## **Step 2: Configure Firebase Credentials**

You need the FCM Server Key from your Firebase project.

### 2A. Find Your FCM Server Key

1.  Go to the [Firebase Console](https://console.firebase.google.com/).
2.  Open your project and go to **Project settings** (click the ⚙️ icon).
3.  Go to the **"Cloud Messaging"** tab.
4.  Under the **"Cloud Messaging API (Legacy)"** section (ensure it's enabled), you will find the **Server key**. Copy it.

### 2B. Update Your `.env` File

1.  Open your `.env` file.
2.  Find the `FCM_SERVER_KEY` line and paste your key:

```dotenv
FCM_SERVER_KEY=YOUR_SERVER_KEY_HERE
```

---

## **Step 3: Disable the Mock Services**

The final step is to tell the application to stop using the fake services.

1.  Open your `.env` file.
2.  Find the line `GOOGLE_WALLET_MOCK=true`.
3.  You can either **delete this line entirely** or change it to `false`:
    ```dotenv
    GOOGLE_WALLET_MOCK=false
    ```

---

## **Step 4: Clear All Application Caches**

To ensure Laravel uses your new live credentials, you must clear all of its caches.

1.  Stop your PHP server (press `Ctrl + C`).
2.  In your terminal, run this command:
    ```bash
    php artisan optimize:clear
    ```
3.  You will see a series of success messages confirming the caches are cleared.

---

## **Step 5: Restart and Test**

You are now ready to run in live mode.

1.  Restart your server: `php -S localhost:9000 -t public`
2.  Run the API test commands again. The `save_link` you receive for a new card should now be a **real, working link** that you can open on an Android device to save the pass to your Google Wallet.
```powershell
# Example: Create a new card in live mode
Invoke-RestMethod -Uri http://localhost:9000/api/loyalty-cards -Method POST -ContentType 'application/json' -Body '{"user_name": "Live Test User"}'
```
Your application is now fully configured to work with the live Google services. 