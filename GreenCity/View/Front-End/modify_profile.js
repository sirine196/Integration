function modify(){
    nom = document.getElementById("nom").value;
    prenom = document.getElementById("prenom").value;
    mail = document.getElementById("mail").value;
    old_mdp = document.getElementById("mdp").value;
    new_mdp = document.getElementById("new_mdp").value;
    cmdp = document.getElementById("cmdp").value;
    console.log(nom,prenom,mail,mdp,old_mdp,new_mdp,cmdp);

    //Controle de saisie
    if(nonAlphabetique(nom) || nom.length==0){
        alert("nom doit etre alphabetique");
        return false;

    }else if(nonAlphabetique(prenom || prenom.length==0)){
        alert("prenom doit etre alphabetique");
        return false;

    }else if(mailInvalide(mail || mail.length==0)){
        alert("veillez verifier votre adresse mail");
        return false;

    }else if(new_mdp!=cmdp){
        alert("Nouveau mot de passe non conforme");
        return false;

    }else if(new_mdp==old_mdp){
        alert("Veillez choisir un nouveau mot de passe");
        return false;

    }
    return true
}