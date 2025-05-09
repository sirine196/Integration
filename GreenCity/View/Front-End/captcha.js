let stepIndex = 0;
let currentScenario = "";
let captchaFailed = false;

document.addEventListener('DOMContentLoaded', function() {
    const imgCaptcha = document.getElementById('img_captcha');
    const stepsDiv = document.getElementById('steps');
    const mapCaptcha = document.getElementById('map_captcha');

    const randomImg = Math.floor(Math.random() * 3);

    if (randomImg === 0) {
        currentScenario = "chat";
        imgCaptcha.src = "../../Ressources/captcha/chat.jpg";
        stepsDiv.innerHTML = `
            <span id="step1">Patte</span> -
            <span id="step2">Oeil</span> -
            <span id="step3">Nez</span> -
            <span id="step4">Queue</span>
        `;
        mapCaptcha.innerHTML = `
            <area shape="poly" coords="128,390,240,398,219,454,204,484,163,484,145,470,111,463,112,440,134,425" href="#" onclick="clickStep('patte'); return false;" alt="Patte">
            <area shape="poly" coords="160,178,186,191,184,209,160,202" href="#" onclick="clickStep('oeil'); return false;" alt="Oeil1">
            <area shape="poly" coords="222,206,240,208,238,223,227,227,214,222" href="#" onclick="clickStep('oeil'); return false;" alt="Oeil2">
            <area shape="circle" coords="192,243,17" href="#" onclick="clickStep('nez'); return false;" alt="Nez">
            <area shape="poly" coords="228,431,250,434,278,443,329,429,338,442,292,472,236,472,224,449" href="#" onclick="clickStep('queue'); return false;" alt="Queue">
        `;
    }
    else if (randomImg === 1) {
        currentScenario = "ville";
        imgCaptcha.src = "../../Ressources/captcha/ville.jpg";
        stepsDiv.innerHTML = `
            <span id="step1">Tunnel</span> -
            <span id="step2">Train</span> -
            <span id="step3">Voiture Blanche</span>
        `;
        mapCaptcha.innerHTML = `
            <area shape="poly" coords="154,144,173,144,175,172,152,171" href="#" onclick="clickStep('tunnel'); return false;" alt="Tunnel">
            <area shape="poly" coords="92,87,139,88,154,114,155,164,144,177,82,176,84,95" href="#" onclick="clickStep('train'); return false;" alt="Train">
            <area shape="poly" coords="180,159,195,159,201,167,201,173,175,173,175,167" href="#" onclick="clickStep('voitureB'); return false;" alt="voitureB">
        `;
    }
    else {
        currentScenario = "machine";
        imgCaptcha.src = "../../Ressources/captcha/machine.png";
        stepsDiv.innerHTML = `
            <span id="step1">Gyrophare</span> -
            <span id="step2">Logo</span> -
            <span id="step3">Masse Avant</span>
        `;
        mapCaptcha.innerHTML = `
            <area shape="rect" coords="194,48,201,56" href="#" onclick="clickStep('gyrophare'); return false;" alt="Gyrophare">
            <area shape="rect" coords="222,120,238,128" href="#" onclick="clickStep('logo'); return false;" alt="Logo">
            <area shape="rect" coords="227,158,277,183" href="#" onclick="clickStep('masseAvant'); return false;" alt="Masse Avant">
        `;
    }

    imgCaptcha.addEventListener('click', function(event) {
        if (!event.target.closest('area') && !captchaFailed) {
            captchaFailed = true;
            alert("Vous avez cliqué en dehors des zones prévues. Veuillez recommencer.");
            window.location.reload();
        }
    });
});

const correctOrder = {
    chat: ["patte", "oeil", "nez", "queue"],
    ville: ["tunnel", "train", "voitureB"],
    machine: ["gyrophare", "logo", "masseAvant"]
};

function clickStep(part) {
    if (captchaFailed) return;

    const expectedPart = correctOrder[currentScenario][stepIndex];
    if (part === expectedPart) {
        stepIndex++;
        document.getElementById(`step${stepIndex}`).style.color = "green";

        if (stepIndex === correctOrder[currentScenario].length) {
            window.location.href = "interface.php";
        }
    } else {
        alert("Incorrect ! Veuillez recommencer.");
        window.location.reload();
    }
}
