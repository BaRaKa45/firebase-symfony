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

                redirect_post('register', idToken);

            }).catch(function (error) {
                console.log(error.message);
            });
            return false;
        },
        signInFailure: function(error) {
            // For merge conflicts, the error.code will be
            // 'firebaseui/anonymous-upgrade-merge-conflict'.
            if (error.code != 'firebaseui/anonymous-upgrade-merge-conflict') {
                return Promise.resolve();
            }
            // The credential the user tried to sign in with.
            var cred = error.credential;
            // Copy data from anonymous user to permanent user and delete anonymous
            // user.
            // ...
            // Finish sign-in after data is copied.
            return firebase.auth().signInWithCredential(cred);
        }
    },
    signInFlow: 'popup',
    signInSuccessUrl: '/',
    signInOptions: [
        firebase.auth.GoogleAuthProvider.PROVIDER_ID,
        firebase.auth.FacebookAuthProvider.PROVIDER_ID,
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

//REDIRECT WITH POST

/*function redirect_post(link, post_var) {
    var form = '';
    $.each(post_var, function(key, value) {
        form+='<input type="hidden" name="'+key+'" value="'+value+'">';
    });
    $('<form class="hidden" action="'+link+'" method="POST">'+form+'</form>').appendTo('body').submit();
}*/

function redirect_post(link, value) {
    let uid ='<input type="hidden" name="token" value="'+value+'">';
    $('<form class="hidden" action="'+link+'" method="POST">'+uid+'</form>').appendTo('body').submit();
}


