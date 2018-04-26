var email = "";
var motdepasse = "";
function SeConnecter()
{
    var erreur = false;
    var msgerreur = "";
    if(document.getElementById("email").value == "")
    {
        erreur = true;
        msgerreur = msgerreur + "Le champs de l'adresse email est vide !\n";
    }
    if(document.getElementById("password").value == "")
    {
        erreur = true;
        msgerreur = msgerreur + "Le champs du mot de passe est vide !\n";
    }
    if(erreur)
    {
        ons.notification.alert({message: msgerreur, animation: 'none'});
    }
    else
    {
        email = document.getElementById("email").value;
        motdepasse = document.getElementById("password").value
        verificationLogin(email, motdepasse);
    }
}
function verificationLogin(email, motdepasse)
{
    document.getElementById("loading").style.display = 'block';
    DesactiverBouton();
    var xmlhttp;
    xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState == XMLHttpRequest.DONE)
        {
            if(xmlhttp.status == 200)
            {
                if(xmlhttp.responseText == 0)
                {
                    ActiverBouton();
                    ons.notification.alert({message: "Identifiants incorrectes", animation: 'none'});
                    document.getElementById("loading").style.display = 'none';
                }
                else
                {
                    window.localStorage.setItem("email", email);
                    window.localStorage.setItem("password", motdepasse);
                    window.localStorage.setItem("etatbourse", xmlhttp.responseText);
                    document.location.href='bourse.html';
                }
                //document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
            }
            else
            {
                ActiverBouton();
                alert('Pas de connexion internet !')
                document.getElementById("loading").style.display = 'none';
            }
        }
    }
    xmlhttp.open("GET", "http://ppe-gsb.olympe.in/affichageBourse.php?email="+email+"&password="+motdepasse, true);
    xmlhttp.send();
}
function actualiserBourse()
{
    document.getElementById("loading").style.display = 'block';
    actualisebutton.setDisabled(true);
    var xmlhttp;
    xmlhttp = new XMLHttpRequest();
    var email = window.localStorage.getItem("email");
    var motdepasse = window.localStorage.getItem("password");
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState == XMLHttpRequest.DONE)
        {
            if(xmlhttp.status == 200)
            {
                if(xmlhttp.responseText == 0)
                {
                    ons.notification.alert({message: "Identifiants incorrectes", animation: 'none'});
                    actualisebutton.setDisabled(false);
                    document.getElementById("loading").style.display = 'none';
                }
                else
                {
                    document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
                    actualisebutton.setDisabled(false);
                    document.getElementById("loading").style.display = 'none';
                }
            }
            else
            {
                alert('Pas de connexion internet !')
                actualisebutton.setDisabled(false);
                document.getElementById("loading").style.display = 'none';
            }
        }
    }
    xmlhttp.open("GET", "http://ppe-gsb.olympe.in/affichageBourse.php?email="+email+"&password="+motdepasse, true);
    xmlhttp.send();
}
function RappelIdentifiant()
{
    if(window.localStorage.getItem("email"))
    {
        document.getElementById("email").value = window.localStorage.getItem("email");
    }
    if(window.localStorage.getItem("password"))
    {
        document.getElementById("password").value = window.localStorage.getItem("password");
    }
}
function DesactiverBouton()
{
    connectbutton.setDisabled(true);
}
function ActiverBouton()
{
    connectbutton.setDisabled(false);
}
function AfficherBourse()
{
    document.getElementById("myDiv").innerHTML = window.localStorage.getItem("etatbourse");
}