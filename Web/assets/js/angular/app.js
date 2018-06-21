
angular.module('IGCApp', ['ngResource', 'mwl.calendar', 'ui.bootstrap'])
    .controller('CalCtrl', CalCtrl)
    .factory('HotelPrices', ['$resource', function($resource) {
        return $resource('/igc2/v2/Web/client/:id/relationHotel/:idHotel/prix/json', null,     {});
    }])
    .factory('alertModal', alertFactory);


function CalCtrl($scope, moment, alertModal, calendarConfig, HotelPrices) {
    $scope.calendarView = 'week';
    $scope.viewDate = new Date();
    $scope.loading = true;
    $scope.changeCalendar = function(id) {
        $scope.viewName = id;
    };

    var actions = [{
        label: '<i class="fa fa-pencil"></i>',
        onClick: function(args) {
            alertModal.show(args.calendarEvent);
        }
    }];

    $scope.sources = {};

    $scope.load = function(idHotel, idClient) {
        HotelPrices.get({id:idClient, idHotel:idHotel}).$promise
            .then(function(data) {
                $scope.typeList.forEach(function(i) {
                    data[i.id].forEach(function(element) {
                        element.actions = actions;
                        element.startsAt = moment(element.startsAt).toDate();
                        element.endsAt = moment(element.startsAt).toDate();
                        //element.color = calendarConfig.colorTypes.transparency;
                    });
                });

                $scope.sources = data;
                $scope.changeCalendar($scope.typeList[0].id);
                $scope.loading = false;
            }).catch(function(error) {
                console.log(error);
                alert("An error has occurred while fetching data..");
        });
    };
}


function alertFactory($uibModal) {

    function show(event) {
        return $uibModal.open({
            templateUrl: '/assets/html/modalContent.html',
            controller: function($scope, $http) {
                var vm = this;
                vm.event = event;
                console.log(event);
                $scope.save = function(cb) {
                    var body = {};

                    if (!vm.event.dispo) {
                        if (vm.event.prix)
                            body.prix = vm.event.prix;
                        else
                            body.taux_marge = vm.event.marge;
                    } else {
                        body.nbr_chambre_contrat = vm.event.nbr_chambre_contrat;
                        body.nbr_chambre_journal = vm.event.nbr_chambre_journal;
                        body.retrocession_journal = vm.event.retrocession_journal;
                        body.retrocession_contrat = vm.event.retrocession_contrat;
                        body.min_stay = vm.event.min_stay;
                        body.max_stay = vm.event.max_stay;
                        body.etat_fermer_contrat = vm.event.etat_fermer_contrat;
                        body.etat_fermer_journal = vm.event.etat_fermer_journal;
                    }

                    $http.post(vm.event.url, body).then(function(ok) {
                            vm.event.title = ok.data.title;
                        cb();
                    }, function(error) {
                        console.log(error);
                    });
                }
            },
            controllerAs: 'vm'
        });
    }

    return {
        show: show
    };

}