// Les fonctions de ce fichier doivent être OBLIGATOIREMENT préfixées de 'export' : 
export function showAlertMessage(titre, sousTitre, nature) {
    var type = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var classePerso = {
        cancelButton: "btn btn-outline-danger btn-cancel"
    };

    var _modalSweet2 = Swal.mixin({
        customClass: classePerso,
        buttonsStyling: false
    });

    if (type == "info") {
        _modalSweet2.fire({
            title: titre,
            html: sousTitre,
            icon: nature,
            showCancelButton: false,
            showConfirmButton: false,
            cancelButtonText: "Fermer",
            timer: 3000,
            // On n'autorise pas le clic en dehors de l'alert,
            allowOutsideClick: false
        });
    } else if (type == "close") {
        _modalSweet2.fire({
            title: titre,
            html: sousTitre,
            icon: nature,
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: "Fermer",
            // On n'autorise pas le clic en dehors de l'alert,
            allowOutsideClick: false
        });
    } else {
        _modalSweet2.fire({
            title: titre,
            html: sousTitre,
            icon: nature,
            showCancelButton: false,
            showConfirmButton: false,
            timer: 1500,
            // On n'autorise pas le clic en dehors de l'alert,
            allowOutsideClick: false
        });
    }
}