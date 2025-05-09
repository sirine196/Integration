function create(){
    nom = document.getElementById("nom").value;
    prenom = document.getElementById("prenom").value;
    mail = document.getElementById("mail").value;
    mdp = document.getElementById("mdp").value;
    cmdp = document.getElementById("cmdp").value;
    console.log(nom,prenom,mail,mdp,cmdp);

    //Controle de saisie
    if(nonAlphabetique(nom) || nom.length==0){
        alert("nom doit etre alphabetique");
        return false;

    }else if(nonAlphabetique(prenom) || prenom.length==0){
        alert("prenom doit etre alphabetique");
        return false;

    }else if(mailInvalide(mail || mail.length==0)){
        alert("veillez verifier votre adresse mail");
        return false;

    }else if(mdp!=cmdp || mdp.length==0){
        alert("veillez verifier votre mot de passe");
        return false;

    }
    return true;
}

