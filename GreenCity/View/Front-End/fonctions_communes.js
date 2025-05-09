function nonAlphabetique(chaine){
    for (i=0;i<chaine.length;i++){
        if(!( (chaine.charCodeAt(i)>64 && chaine.charCodeAt(i)<91) || (chaine.charCodeAt(i)>96 && chaine.charCodeAt(i)<123) )){
            alert("nom doit etre alphabetique");
            return false;
        }
    }
    
}

function mailInvalide(chaine){
    if (!(chaine.endsWith("@gmail.com") || chaine.endsWith("@yahoo.com") || chaine.endsWith("@outlook.com") || chaine.endsWith("@hotmail.com") || chaine.endsWith("@esprit.tn"))){
        alert(chaine+" Votre mail n'est pas Valide");
        return false;
    }
}