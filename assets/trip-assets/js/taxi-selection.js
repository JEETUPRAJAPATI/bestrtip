$(document).ready(function () {
// fetch('ajax/get-vehicles.php')
//   .then(response => response.json())
//   .then(vehicles => {
//     console.log(vehicles); // Now vehicles is in the same format as your example
//     const vehicles = vehicles;
//     vehicles.forEach(vehicle => {
//         // Example: render to console or DOM
//       console.log(vehicle.name, vehicle.type, vehicle.bag);
//       //vehicles = data;
//     generateVehicleCards();
//     });
//   })
//   .catch(error => console.error('Error fetching vehicle data:', error));
let vehicles = [];


    // Sample data for vehicles
    // const vehicles = [
    //     { id: 1, bag: 3, count: 2, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 2, bag: 2, count: 4, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 3, bag: 3, count: 5, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 4, bag: 1, count: 7, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 5, bag: 5, count: 1, name: 'Toyota Camry', type: 'Sedan', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 6, bag: 5, count: 8, name: 'Honda CR-V', type: 'SUV', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 7, bag: 4, count: 9, name: 'Ford F-150', type: 'Truck', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 8, bag: 8, count: 4, name: 'Tesla Model 3', type: 'Electric', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 9, bag: 9, count: 6, name: 'BMW X5', type: 'SUV', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    //     { id: 10, bag: 1, count: 6, name: 'Audi A4', type: 'Sedan', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    // ];

    // const drivers = [
    //     {
    //         id: 1,
    //         name: 'Saif Smith',
    //         contactNo: '+91-1234567890',
    //         completedTrips: 16,
    //         Experience: "since 2022",
    //         carnumber: "UP-1234",
    //         email: 'saif.smith@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-8791-XYZ',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 2,
    //         name: 'John Doe',
    //         contactNo: '+91-0987654342',
    //         completedTrips: 14,
    //         Experience: "since 2022",
    //         carnumber: "UP-1234",
    //         email: 'john.doe@example.com',
    //         rating: 4,
    //         encryptedTrips: 'TRP-2345-ABC',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 3,
    //         name: 'Emily Johnson',
    //         contactNo: '+91-3123445434',
    //         Experience: "since 2022",
    //         carnumber: "UP-1234",
    //         completedTrips: 18,
    //         email: 'emily.johnson@example.com',
    //         rating: 5,
    //         encryptedTrips: 'TRP-3456-DEF',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 4,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 5,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 6,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 7,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 8,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 9,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    //     {
    //         id: 10,
    //         name: 'Michael Brown',
    //         contactNo: '+91-3456367354',
    //         carnumber: "UP-1234",
    //         completedTrips: 10,
    //         Experience: "since 2022",
    //         email: 'michael.brown@example.com',
    //         rating: 3,
    //         encryptedTrips: 'TRP-4567-GHI',
    //         image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    //     },
    // ];

    // State variables
    let currentVehicleIndex = 0;
    let selectedVehicleId = null;
    let selectedDriverId = null;
    let selections = {};
    let completedVehicles = [];
$.getJSON('ajax/get-vehicles.php', function(data) {
    vehicles = data;
    //alert(vehicle);
   generateVehicleCards();
});
let drivers = [];
$.getJSON('ajax/get-driver.php', function(data) {
    drivers = data;
    //alert(drivers);
   generateDriverCards();
});
    // Generate vehicle cards
    function generateVehicleCards() {
        $('#vehiclesContainer').empty();

        vehicles.forEach((vehicle, index) => {
           // alert(vehicle.id);
            const isSelected = (vehicle.id === selectedVehicleId);
            const isDisabled = completedVehicles.includes(vehicle.id);

            const cardClass = isDisabled ? 'vehicle-card disabled' : (isSelected ? 'vehicle-card selected' : 'vehicle-card');

            const checkIcon = isSelected ? '<div class="absolute top-2 right-2 bg-indigo-600 text-white rounded-full w-6 h-6 flex items-center justify-center"><i class="fas fa-check"></i></div>' : '';

            const vehicleCard = `
                <div
                    class="relative ${cardClass} bg-white p-3 rounded-xl shadow-md flex-shrink-0"
                    data-vehicle-id="${vehicle.id}"
                >
                    ${checkIcon}

                    <img 
                        src="${vehicle.image}" 
                        alt="${vehicle.name}" 
                        class="w-full h-32 object-cover rounded-lg mb-1"
                    />

                    <article class="flex justify-between items-center mb-1">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">${vehicle.name}</h3>
                            <p class="text-sm text-gray-500">${vehicle.type}</p>
                        </div>
                        <p class="text-theme mb-3 pb-1 text-xl font-bold">× ${vehicle.count}</p>
                    </article>

                    <div class="flex items-center justify-between text-sm text-gray-700 mt-2">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-suitcase-rolling text-theme"></i>
                            <span>${vehicle.bag} bag</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user text-theme"></i>
                            <span>${vehicle.people} People</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-start gap-4 mt-1 text-sm text-gray-700">
                        <div class="tooltip-container relative">
                            <i class="fa-solid fs-6 text-theme fa-cookie-bite cursor-pointer hover:text-indigo-600" data-bs-toggle="tooltip" data-bs-placement="top" title="Complementary Snacks"></i>
                            <span class="tooltip-text hidden absolute bottom-full left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 mb-1 whitespace-nowrap">Complementary Snacks</span>
                        </div>
                        <div class="tooltip-container relative">
                            <i class="fa-solid fs-6 text-theme fa-wine-bottle cursor-pointer hover:text-indigo-600" data-bs-toggle="tooltip" data-bs-placement="top" title="Bottled Water Included"></i>
                            <span class="tooltip-text hidden absolute bottom-full left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 mb-1 whitespace-nowrap">Bottled Water Included</span>
                        </div>
                        <div class="tooltip-container relative">
                            <i class="fas fs-6 text-theme fa-sign cursor-pointer hover:text-indigo-600" data-bs-toggle="tooltip" data-bs-placement="top" title="Meet & Greet Service"></i>
                            <span class="tooltip-text hidden absolute bottom-full left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 mb-1 whitespace-nowrap">Meet & Greet Service</span>
                        </div>
                    </div>
                </div>
            `;
//alert(vehicleCard);
            $('#vehiclesContainer').append(vehicleCard);
        });

        // Initialize tooltips
        if (typeof bootstrap !== 'undefined') {
            // Bootstrap tooltips (if Bootstrap is included)
            $('[data-bs-toggle="tooltip"]').tooltip();
        } else {
            // Custom tooltips implementation
            $('.tooltip-container').hover(
                function () {
                    $(this).find('.tooltip-text').removeClass('hidden');
                },
                function () {
                    $(this).find('.tooltip-text').addClass('hidden');
                }
            );
        }

        // Check if there's a selected vehicle with no selection yet
        if (selectedVehicleId && !selections[selectedVehicleId]) {
            generateDriverCards();
        }

        // Attach click handlers to vehicle cards
        $('.vehicle-card:not(.disabled)').click(function () {
            selectedVehicleId = $(this).data('vehicle-id');
            $('.vehicle-card').removeClass('selected');
            $(this).addClass('selected');
            generateDriverCards();
        });
    }

    // Generate driver cards with the updated data structure
    function generateDriverCards() {
        $('#driversContainer').empty();

        if (!selectedVehicleId) {
            return;
        }

        drivers.forEach(driver => {
            //alert(driver.id);
            const isSelected = (driver.id === selectedDriverId);
            // Encrypt sensitive information - showing only partial data
            const maskedPhone = driver.contactNo.replace(/(\+\d{2})-(\d{6})(\d{4})/, '$1-******$3');
            const emailParts = driver.email.split('@');
            const maskedEmail = emailParts[0].substring(0, 2) + '***@' + emailParts[1];

            const driverCard = `
            <div class="relative driver-card ${isSelected ? 'selected' : ''} bg-white rounded-lg shadow-md overflow-hidden" data-driver-id="${driver.id}">
                <!-- Card Header with Driver Info -->
                <div class="p-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden mr-3">
                            <img src="${driver.image}" alt="${driver.name}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold">${driver.name}</h3>
                            <div class="flex items-center">
                                <div class="rating text-orange-500">
                                    ${Array(driver.rating).fill('<i class="fas fa-star"></i>').join('')}
                                    ${Array(5 - driver.rating).fill('<i class="fas fa-star text-gray-300"></i>').join('')}
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">${driver.Experience}</p>
                        </div>
                        ${isSelected ? '<div class="absolute top-3 right-3 bg-indigo-600 text-white rounded-full w-6 h-6 flex items-center justify-center"><i class="fas fa-check"></i></div>' : ''}
                    </div>

                    <!-- Contact Information - Privacy Protected -->
                    <div class="mt-3 bg-gray-50 rounded p-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone-alt text-theme w-5"></i>
                            <span class="ml-2">${maskedPhone}</span>
                        </div>
                        <div class="flex items-center text-sm mt-1">
                            <i class="fas fa-envelope text-theme w-5"></i>
                            <span class="ml-2">${maskedEmail}</span>
                        </div>
                         <div class="flex items-center text-sm mt-1">
                            <i class="fa-solid fa-car-side text-theme w-5"></i>
                            <span class="ml-2">${driver.carnumber}</span>
                        </div>
                    </div>
                </div>

                <!-- Driver Stats -->
                <div class="px-3 pb-3">
                    <div class="flex justify-between mb-3">
                        <div class="text-center bg-purple-100 rounded p-2 w-1/2 mr-1">
                            <p class="font-bold text-lg text-theme">${driver.completedTrips}</p>
                            <p class="text-xs text-gray-600">Completed Trips</p>
                        </div>
                        <div class="text-center bg-purple-100 rounded p-2 w-1/2 ml-1">
                            <p class="font-bold text-lg text-theme">ID</p>
                            <p class="text-xs font-mono text-gray-600 truncate">${driver.encryptedTrips}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between">
                        <button class="px-2 py-1 text-xs border border-theme text-theme rounded hover:bg-purple-900 hover:text-white transition-colors flex items-center">
                            <i class="fas fa-images mr-1"></i> Trip Album
                        </button>
                        <button class="px-2 py-1 text-xs border border-theme text-theme rounded hover:bg-purple-900 hover:text-white transition-colors flex items-center">
                            <i class="fas fa-history mr-1"></i> Rating History
                        </button>
                    </div>
                    
                    <!-- Main Action Button -->
                    <button class="w-full mt-3 bg-theme text-white py-2 rounded hover:bg-indigo-700 transition-colors">
                        ${isSelected ? 'Selected' : 'Select Driver'}
                    </button>
                </div>
            </div>
        `;
    //alert(driverCard);
            $('#driversContainer').append(driverCard);
        });

        // Attach click handlers to driver cards
        $('.driver-card').click(function () {
            selectedDriverId = $(this).data('driver-id');
            $('.driver-card').removeClass('selected');
            $(this).addClass('selected');
            $('.driver-card button.w-full').text('Select Driver');
            $(this).find('button.w-full').text('Selected');
            $('#continueBtn').prop('disabled', false);
        });
    }
    // Improved scroll functions for vehicle carousel
    $('#prevVehicle').click(function () {
        const container = document.getElementById('scrollContainer');
        const scrollAmount = -280; // Card width + margin
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    $('#nextVehicle').click(function () {
        const container = document.getElementById('scrollContainer');
        const scrollAmount = 280; // Card width + margin
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    // Continue button click handler
    $('#continueBtn').click(function () {
        if (selectedVehicleId && selectedDriverId) {
            // Save the selection
            selections[selectedVehicleId] = selectedDriverId;
            completedVehicles.push(selectedVehicleId);

            // Check if all vehicles have been assigned
            if (completedVehicles.length === vehicles.length) {
                // showSummary();
            } else {
                // Find the next unassigned vehicle
                const nextVehicle = vehicles.find(v => !completedVehicles.includes(v.id));
                if (nextVehicle) {
                    selectedVehicleId = nextVehicle.id;
                    selectedDriverId = null;
                    $('#continueBtn').prop('disabled', true);
                    generateVehicleCards();
                }
            }
        }
    });



    // Finish button click handler
    $('#finishBtn').click(function () {
        alert('Selection process completed!');
        // Here you would typically submit the data or redirect to another page
    });

    // Initialize the interface
    generateVehicleCards();

    // Auto-select the first vehicle on page load
    setTimeout(() => {
        if (!selectedVehicleId) {
            $('.vehicle-card:first').click();
        }
    }, 500);
});