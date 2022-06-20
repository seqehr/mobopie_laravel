<script type="module">
    // Import the functions you need from the SDKs you need

    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js";

    import {
        getAnalytics
    } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics.js";

    // TODO: Add SDKs for Firebase products that you want to use

    // https://firebase.google.com/docs/web/setup#available-libraries


    // Your web app's Firebase configuration

    // For Firebase JS SDK v7.20.0 and later, measurementId is optional

    const firebaseConfig = {

        apiKey: "AIzaSyCDjKZgFL95MTcMErxwAStFTYoofQYWOhs",

        authDomain: "chat-app-b88e9.firebaseapp.com",

        databaseURL: "https://chat-app-b88e9-default-rtdb.europe-west1.firebasedatabase.app",

        projectId: "chat-app-b88e9",

        storageBucket: "chat-app-b88e9.appspot.com",

        messagingSenderId: "362715155241",

        appId: "1:362715155241:web:08a9326b7a792312de4538",

        measurementId: "G-WT1WSYMEF1"

    };


    // Initialize Firebase

    const app = initializeApp(firebaseConfig);

    const analytics = getAnalytics(app);
</script>