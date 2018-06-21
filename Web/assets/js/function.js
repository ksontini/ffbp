$(function () {
// Datepicker
    $('.calendrier').datepicker({
        inline: true,
        dateFormat: "yy-mm-dd"

    });

});

function show_input_marge_package(type_montage) {
    if (type_montage == 2 || type_montage == 3) {
        $("#ligne_marge_package").show();
    } else {
        $("#ligne_marge_package").hide();
    }
}

$(document).ready(function () {

    /**
     * DropDown Menu Script
     */

    $('.exp').collapsible({
        defaultOpen: 'current',
        cookieName: 'navAct',
        cssOpen: 'active',
        cssClose: 'inactive',
        speed: 200
    });

    $('.opened').collapsible({
        defaultOpen: 'opened,toggleOpened',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });

    $('.closed').collapsible({
        defaultOpen: '',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });

    /**
     * Menu Dashbord
     */

    if (!$('body').hasClass('altlayout')) {
        $("#navigation").superfish({speed: 'fast', delay: 300, autoArrows: false});
    }

    if (!$('body').hasClass('altlayout')) {
        $("#liens_index").superfish({speed: 'fast', delay: 300, autoArrows: false});
    }

    /**
     * DropDown Informations Générales Montage
     */
    //Hide (Collapse) the toggle containers on load
    $(".bg_blanc").show();

    //Slide up and down on hover
    $(".titre_h3").click(function () {
        $(this).next(".bg_blanc").slideToggle();
    });

    //Hide (Collapse) the toggle containers on load
    $(".bg_montage_blanc").show();

    //Slide up and down on hover
    $(".titre_montage_h3").click(function () {
        $(this).next(".bg_montage_blanc").slideToggle();
    });

    /**
     * RÉCAPITULATIF DOSSIER
     */
    if (!$('body').hasClass('altlayout')) {
        $(".menu_search").superfish({speed: 'fast', delay: 300, autoArrows: false});
    }

    if (!$('body').hasClass('altlayout')) {
        $(".menu_search_renitia").superfish({speed: 'fast', delay: 300, autoArrows: false});
    }
    $(function () {
        $(".someClass").tipTip({maxWidth: "600", edgeOffset: 10});
    });
});

/**
 * loader functions
 */
function patientez() {
    $.blockUI({message: '<div class="spacing_h2"> <p class="bg_busy">Veuillez patienter ... </p>   </div>'});
}

function remove_patientez() {
    $.unblockUI();
}

$(document).ready(function () {
    $('#login').show().animate({opacity: 1}, 2000);
    $('.bienvenue_igc').show().animate({opacity: 1, top: '40%'}, 800, function () {
        $('.bienvenue_igc').show().delay(1200).animate({opacity: 1, top: '12%'}, 300, function () {
            $('.formLogin').animate({opacity: 1, left: '0'}, 300);
            $('.userbox').animate({opacity: 0}, 200).hide();
        });

    })
    $(".on_off_checkbox").iphoneStyle();
    $('.tip a ').tipsy({gravity: 'sw'});
    $('.tip input').tipsy({trigger: 'focus', gravity: 'w'});
});
$('.userload').click(function (e) {
    $('.formLogin').animate({opacity: 1, left: '0'}, 300);
    $('.userbox').animate({opacity: 0}, 200, function () {
        $('.userbox').hide();
    });
});

$('#but_login').click(function (e) {
    if (document.formLogin.username.value == "" || document.formLogin.password.value == "") {
        showError("Veuillez entrer votre login et/ou votre mot de passe.");
        $('.inner').jrumble({x: 4, y: 0, rotation: 0});
        $('.inner').trigger('startRumble');
        setTimeout('$(".inner").trigger("stopRumble")', 500);
        setTimeout('hideTop()', 5000);
        return false;
    }
    hideTop();

    loading('Checking', 1);
    setTimeout("unloading()", 2000);
    setTimeout("Login()", 2500);
});


$('#alertMessage').click(function () {
    hideTop();
});

function showError(str) {
    $('#alertMessage').addClass('error').html(str).stop(true, true).show().animate({
        opacity: 1,
        left: '10'
    }, 500);

}

function showSuccess(str) {
    $('#alertMessage').addClass('success').html(str).stop(true, true).show().animate({
        opacity: 1,
        left: '10'
    }, 500);
}

function hideTop() {
    $('#alertMessage').animate({opacity: 0, left: '0'}, 500, function () {
        $(this).hide();
    });
}

function loading(name, overlay) {
    $('body').append('<div id="overlay"></div><div id="preloader">' + name + '..</div>');
    if (overlay == 1) {
        $('#overlay').css('opacity', 0.1).fadeIn(function () {
            $('#preloader').fadeIn();
        });
        return false;
    }
    $('#preloader').fadeIn();
}

function unloading() {
    $('#preloader').fadeOut('fast', function () {
        $('#overlay').fadeOut();
    });
}

$(document).ready(function() {
    showError("Opération échouée");
    $('.inner').jrumble({x: 4, y: 10, rotation: 0});
    $('.inner').trigger('startRumble');
    setTimeout('$(".inner").trigger("stopRumble")', 500);
    setTimeout('hideTop()', 5000);

    /**
     *  Add Row for participants
     */
    $('.ajouter_enregistrement').on('click', function () {
        $('.tab_add_room > tbody#tab_add_room:last-child').append('<tr>' +
            '<td width="20%"><input type="hidden" name="ordre_participants[]" id="ordre_participants[]" value="" />' +
            '   <input type="text" name="nom_prenom_participants[]" id="nom_prenom_participants[]" value="" autocomplete="off" />' +
            '</td>' +
            '<td width="20%"></td><td width="10%" class="delete"><a  href="#"> Supprimer </a></td>' +
            '</tr>');
    });

    /**
     * Remove row for participants
     */
    $(document).on('click', '.tab_add_room td.delete> a', function () {
        var self = $(this).parent().parent();

        self.hide('slow', function () {
            self.remove();
        });

        return false;
    });

    $('#groupe_client').on('change', function () {
        window.location.href = $('#participants_form').attr('action') + "?id_groupe_client=" + $(this).val();
    });


    /**
     *  Add Row Date Montage
     */
    $('#addDate').on('click', function () {
        var ikey = parseInt($('#dates .order').last().val())+1;
        $('#nbrDaysPresta').val(ikey);
        var typeProduit = $('#id_type_produits').val();
        if(typeProduit==1){
            var input = 'Du : * <input class="calendrier small_inp_cal" name="datedu_'+ikey+'" type="text" id="datepicker_du_sejour_'+ikey+'" autocomplete="off" value="" onChange="checkDifferenceDate(\'datepicker_du_sejour_'+ikey+'\', \'datepicker_au_sejour_'+ikey+'\', 1);" />' +
                'Au : * <input class="calendrier small_inp_cal" name="dateau_'+ikey+'" type="text" id="datepicker_au_sejour_'+ikey+'" autocomplete="off" value=""   onChange="checkDifferenceDate(\'datepicker_du_sejour_'+ikey+'\',  \'datepicker_au_sejour_'+ikey+'\', 2);"/>'
        }
        else
        {
            var input ='Le :  <input class="calendrier small_inp_cal" name="datedu_'+ikey+'" type="text" id="datepicker_du_sejour_'+ikey+'" autocomplete="off" value=""  />'
        }
        $('#dates').append('<p class="date_sur_mesure">' +
            '<input type="hidden" class="order" value="'+ikey+'"/>'+
            '<label>Date '+ikey+' :</label>'+
            input
            +
            '<button class="trash_supp" ><span class="icon-trash"></span></button>' +
            '</p>')
    });

    /**
     * Remove Row Date Montage
     */
    $(document).on('click', '.trash_supp', function () {
        var self = $(this).parent();

        self.hide('slow', function () {
            self.remove();
        });

        return false;
    });
});
/**
 * process form participants
 * @returns {boolean}
 */
$(document).on("submit", 'form#participants_form', function(e) {
    e.preventDefault();
    var data = {
        names:[]
    };

    var formData = $('#participants_form').serializeArray();
    formData.forEach(function(elm) {
        if (elm.name == "nom_prenom_participants[]")
            data.names.push(elm.value);
    });
//alert(window.location.href);
    $.ajax({
        url: window.location.href,
        type: 'POST',
        data: data,
        success: function(code, statut) {
            location.reload();
        }
    });
    return false;
});

$(document).ready(function() {
    /**
     *  Add Row for repartition
     */
    $('.ajouter_enregistrement').on('click', function() {
        var select = $('.liste_form_num_chambre_grande').clone();
        $('.tab_add_room > tbody#repartition_body:last-child').append('<tr>' +
            '<td width="20%"><input type="hidden" name="ordre_repartition_package[]" id="ordre_repartition_package[]" value="" />' +
            '<select class="liste_form_num_chambre_grande" name="type_chambre[]">' +
            select.html() + '</select></td>' +
            '<td width="10%"><input type="text" name="nbr_pax[]" id="nbr_pax[]" onBlur="if (this.value == \'\')' +
            'this.value = \'\'" onFocus="if (this.value == \'\') '+
            'this.value = \'\'" value="" autocomplete="off" /></td> ' +
            '<td width="20%"></td>' +
            '<td width="10%" class="delete"><a  href="#"> Supprimer </a></td>' +
            '</tr>');

    });

    /**
     *
     */

    $(document).on("submit", 'form#form_num_chambre', function(e) {
        e.preventDefault();
        var data = [];
        var formData = $('#form_num_chambre').serializeArray();
        var obj;

        formData.forEach(function(elm, index) {
            if (elm.name == 'ordre_repartition_package[]')
            {
                obj = {};
            }
            if (elm.name == 'id_type_chambre[]')
                obj.id_type_chambre = elm.value;

            if (elm.name == 'nbr_pax[]')
            {
                obj.nbr_pax = elm.value;
                data.push(obj);
            }
        });

        $.ajax({
            url: window.location.href,
            type: 'POST',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(res) {
                if (!res.ok)
                {
                    showError('Total pax incorrect.');
                    remove_patientez();
                }
                else
                    location.reload();

            },
            error: function(xls, error) {
                console.log(xls);
                console.log(error);
                //location.reload();
            }
        });

        return false;

    });

});

/**
 * Minimum Base
 */

$(document).ready(function() {
    $(document).on('click','#ajouter_enregistrement_base', function() {
        $('.tab_add_room > tbody:last-child').append('<tr>' +
            '<td width="10%"><input type="hidden" name="ordre_base_minimum[]" id="ordre_base_minimum[]" value="" />' +
            '<input type="text" name="nbr_pax[]" id="nbr_pax[]" onBlur="" autocomplete="off" /></td>' +
            '' +
            '<td>' +
            '        </td>        <td width="20%"></td>' +
            '    <td width="10%" class="delete"><a  href="#"> Supprimer </a></td><tr/>');
    });

    $(document).on("submit", 'form#form_base_min', function(e) {
        e.preventDefault();
        console.log(e);
        var data = [];
        var formData = $('#form_base_min').serializeArray();


        formData.forEach(function(elm, index) {
            var obj = {};
            console.log(elm);
            if (elm.name == 'nbr_pax[]')
            {
                obj.nbr_pax = elm.value;
                data.push(obj);
            }
        });

        $.ajax({
            url: window.location.href,
            type: 'POST',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),
            success: function(res) {
                if (!res.ok)
                {
                    showError('Total pax incorrect.');
                    remove_patientez();
                }
                else
                    location.reload();

            },
            error: function(xls, error) {
                console.log(xls);
                console.log(error);
            }
        });

        return false;
    });
});

/**
 *  Add Price
 */
$(document).ready(function () {
    $(document).on("submit", 'form#form_tarif', function(e) {
        e.preventDefault();
        var data={};
        var dataTarif = [];
        var dataTarifGroupe = [];
        var dataTarifGratuite = [];
        var dataTarifReduction = [];
        var formDataTarif = $('#form_tarif').serializeArray();
        var objt;
        var objGroupe;
        var objGratuite;
        var objReduction;

        formDataTarif.forEach(function(elm, index) {

            /**
             * Prix par type de chambre
             */
            if (elm.name == 'id_type_chambre')
            {
                objt = {};
                objt.id_type_chambre = elm.value;
            }
            if (elm.name == 'ordre_tarif')
                objt.ordre_tarif = elm.value;

            if (elm.name == 'label_prestation')
                objt.label_prestation = elm.value;

            if (elm.name == 'prix_achat')
                objt.prix_achat = elm.value;

            if (elm.name == 'quantite_achat')
                objt.quantite_achat = elm.value;

            if (elm.name == 'prix_vente')
                objt.prix_vente = elm.value;

            if (elm.name == 'quantite_vente')
            {
                objt.quantite_vente = elm.value;
                dataTarif.push(objt);
            }

            /**
             * Prix Groupé
             */
            if (elm.name == 'prix_achat_groupe')
            {
                objGroupe = {};
                objGroupe.prix_achat_groupe = elm.value;
            }

            if (elm.name == 'qte_achat_groupe')
                objGroupe.qte_achat_groupe = elm.value;

            if (elm.name == 'prix_vente_groupe')
                objGroupe.prix_vente_groupe = elm.value;

            if (elm.name == 'qte_vente_groupe'){
                objGroupe.qte_vente_groupe = elm.value;
                dataTarifGroupe.push(objGroupe);
            }
            /**
             * Gratuitée
             */
            if (elm.name == 'nbr_pax_gratuite_achat')
            {
                objGratuite = {};
                objGratuite.nbr_pax_gratuite_achat = elm.value;
            }

            if (elm.name == 'limit_gratuite_achat')
                objGratuite.limit_gratuite_achat = elm.value;

            if (elm.name == 'nbr_pax_gratuite_vente')
                objGratuite.nbr_pax_gratuite_vente = elm.value;

            if (elm.name == 'limit_gratuite_vente'){
                objGratuite.limit_gratuite_vente = elm.value;
                dataTarifGratuite.push(objGratuite);
            }

            /**
             * Réduction
             */
            if (elm.name == 'label_reduction')
            {
                objReduction = {};
                objReduction.label_reduction = elm.value;
            }

            if (elm.name == 'type_reduction')
                objReduction.type_reduction = elm.value;

            if (elm.name == 'taux_reduction'){
                objReduction.taux_reduction = elm.value;
                dataTarifReduction.push(objReduction);
            }

        });

        data.tarif  = dataTarif;
        data.groupe = dataTarifGroupe;
        data.gratuite = dataTarifGratuite;
        data.discount = dataTarifReduction;

        //console.log(data);

        $.ajax({
            url: window.location.href,
            type: 'POST',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(data),

            success: function(res) {
                if (!res.ok)
                {
                    showError('bug Tarif');
                    remove_patientez();
                }
                else
                    location.reload();

            },
            error: function(xls, error) {
                console.log(xls);
                console.log(error);
                //location.reload();
            }
        });

        return false;

    });
});

/**
 *Get difference between 2 dates
 */
function checkDifferenceDate(idDu,idAu,src) {
    var date1 = new Date($("#"+idDu).val());
    var date2 = new Date($("#"+idAu).val());
    var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24));
    if(diffDays<=0){
        alert("Veuillez entrer une date de fin supérieure à la date de début");
        src==1?$("#"+idDu).focus():$("#"+idAu).focus();
        return false
    }else return true;

}

/**SHOW Product List add-Montage **/

function LoadProducts () {
    var codeService = $('#liste_prestatire').attr('data-code');
    var source_suggestion = $('#source_suggestion').val();
    if (typeof products!== 'undefined'){
        var blockHtml = "<select  class='js-states' name='id_suggestion'>";
        products.forEach(function (element) {

            if (codeService == element.code)
                blockHtml += "<option value='" + element.code + "' selected>" + element.NameText + "</option>";
            else
                blockHtml += "<option value='" + element.code + "'>" + element.NameText + "</option>";

        });
        blockHtml+="</select>";
}

    //alert(source_suggestion);
    $('#liste_prestatire').html(blockHtml);
    if(source_suggestion==0){
        $("#block_recherche").hide();
    }else{
        $("#block_recherche").show();
        $(".js-states").select2();
        $(".select2").css("width","100%");
        //$("select .js-states").attr("name","id_suggestion");

        $('select').on('select2:select', function (evt) {
            var code=this.value;
            $("select option[value='" + code + "']").attr("selected","selected");
            var service = $("select[name='id_suggestion']").find('option:selected').text();
            $("#NomService").val(service);
        });
    }

}

$(document).on('change', "#source_suggestion",LoadProducts );

$(document).ready(LoadProducts);

$(document).on("click", ".modif", function() {
    var parent = $(this).parent();
    var operations = {};
    operations.plus = parent.children('input.plus_solde').attr('checked');
    operations.moins = parent.children('input.moins_solde').attr('checked');
    operations.solde = parent.children('input.argent').val();

    $.ajax({
        url:parent.children('input[name=url]').val(),
        type:'POST',
        data: operations,
        success:function(data) {
            data = JSON.parse(data);
            console.log(data.solde);
            parent.children('input.argent').val(data.solde);
            remove_patientez();
        }
    });
});

$(document).on("click", ".modifMarge", function() {
    var parent = $(this).parent();
    var form = {};
    form.marge = parent.children('input.marge_tab').val();

    $.ajax({
        url:parent.children('input[name=url]').val(),
        type:'POST',
        data: form,
        success:function(data) {
            parent.children('input.marge_tab').val((parseFloat(form.marge)).toFixed(2));
            remove_patientez();
        }
    });
});


$(document).on('click', '#duplicateInterval', function(){
    $("p.interval").first().clone().appendTo( ".hotel_prices" );
});


function Close(self) {
    console.log($(self).parent());
    $(self).parent().remove();
    return false;
}

function addParameter(param, value) {
    var url = new URL(location);

// If your expected result is "http://foo.bar/?x=1&y=2&x=42"
    //url.searchParams.append('x', 42);

// If your expected result is "http://foo.bar/?x=42&y=2"
    url.searchParams.set(param, value);

// Build result
    return url.toString();
}