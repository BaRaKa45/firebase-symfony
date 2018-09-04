const $ = require('jquery');

var config = {
    apiKey: "AIzaSyDGhmweJoS9OGxB6sT1TAH-Jq2OZmUl2_s",
    authDomain: "test-f07f5.firebaseapp.com",
    databaseURL: "https://test-f07f5.firebaseio.com",
    projectId: "test-f07f5",
    storageBucket: "test-f07f5.appspot.com",
    messagingSenderId: "862062835569"
};
firebase.initializeApp(config);

var ui = new firebaseui.auth.AuthUI(firebase.auth());
ui.start('#firebaseui-auth-container', {
    callbacks: {
        signInSuccessWithAuthResult: function(authResult, redirectUrl) {
            firebase.auth().currentUser.getIdToken(true).then(function (idToken) {

                redirect_post('register', {token: idToken});

            }).catch(function (error) {
                console.log(error.message);
            });
            return false;
        },
    },
    signInFlow: 'popup',
    signInSuccessUrl: '/',
    signInOptions: [
        {
            provider: firebase.auth.GoogleAuthProvider.PROVIDER_ID,
        },

        firebase.auth.EmailAuthProvider.PROVIDER_ID,
        {
            provider: firebase.auth.PhoneAuthProvider.PROVIDER_ID,
            recaptchaParameters: {
                type: 'image', // 'audio'
                size: 'normal', // 'invisible' or 'compact'
                badge: 'bottomleft' //' bottomright' or 'inline' applies to invisible.
            },
            defaultCountry: 'FR', // Set default country to the United Kingdom (+44).
        }
    ],
});

//LOGOUT
document.getElementById("logout").addEventListener('click', e =>{
    firebase.auth().signOut().then(function() {
    }).catch(function(error) {
        console.log(error);
    });
});

//CHECK STATE
firebase.auth().onAuthStateChanged(user=>{
    if(user){
        console.log("Connecté");
        var user = firebase.auth().currentUser;
        const name = document.getElementById("user");
        if (user != null) {
            user.providerData.forEach(function (profile) {
                console.log("Sign-in provider: " + profile.providerId);
                console.log("  Provider UID: " + profile.uid);
                console.log("Email Vérifié: " + profile.emailVerified);
                console.log("  Nom: " + profile.displayName);
                name.innerText = profile.displayName;
                console.log("  Email: " + profile.email);
                console.log("  Photo: " + profile.photoURL);
                console.log(profile);
            });
        }
    } else{
        console.log("Déconnecté");
    }
});


//REDIRECT WITH POST

function redirect_post(link, post_var) {
    var form = '';
    $.each(post_var, function(key, value) {
        form+='<input type="hidden" name="'+key+'" value="'+value+'">';
    });
    $('<form class="hidden" action="'+link+'" method="POST">'+form+'</form>').appendTo('body').submit();
}


/*
firebase.auth().onAuthStateChanged(user=>{
    if(user){
        console.log("Connecté");
        var user = firebase.auth().currentUser;

        firebase.auth().currentUser.getIdToken(/!* forceRefresh *!/ true).then(function (idToken) {

            redirect_post('register', {token: idToken});

        }).catch(function (error) {
            console.log(error.message);
        });
    } else{
        console.log("Déconnecté");
    }
});*/
