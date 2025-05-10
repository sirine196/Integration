function cnx(){
    mail = document.getElementById("mail").value;
    mdp = document.getElementById("mdp").value;
    console.log(mail,mdp);

    //Controle de saisie

    console.log(mail)
    if(mailInvalide(mail)){
        alert("veillez verifier votre adresse mail")
        return false

    }
    if (mail === "" || mdp === "") {
        alert("Tous les champs sont requis.")
        return false;
    }

    
    return true;
}