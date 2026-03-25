
const vehicles = [
    { id: 1, bag: 3, count: 2, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 2, bag: 2, count: 4, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 3, bag: 3, count: 5, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 4, bag: 1, count: 7, name: 'Jeep Cherokee', type: 'SUV', people: 4, image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 5, bag: 5, count: 1, name: 'Toyota Camry', type: 'Sedan', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 6, bag: 5, count: 8, name: 'Honda CR-V', type: 'SUV', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 7, bag: 4, count: 9, name: 'Ford F-150', type: 'Truck', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 8, bag: 8, count: 4, name: 'Tesla Model 3', type: 'Electric', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 9, bag: 9, count: 6, name: 'BMW X5', type: 'SUV', people: 5, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
    { id: 10, bag: 1, count: 6, name: 'Audi A4', type: 'Sedan', people: 4, image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2FyfGVufDB8fDB8fHww' },
];

const drivers = [
    {
        id: 1,
        name: 'Saif Smith',
        contactNo: '+91-1234567890',
        completedTrips: 16,
        Experience: "since 2022",
        carnumber: "UP-1234",
        email: 'saif.smith@example.com',
        rating: 3,
        encryptedTrips: 'TRP-8791-XYZ',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 2,
        name: 'John Doe',
        contactNo: '+91-0987654342',
        completedTrips: 14,
        Experience: "since 2022",
        carnumber: "UP-1234",
        email: 'john.doe@example.com',
        rating: 4,
        encryptedTrips: 'TRP-2345-ABC',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 3,
        name: 'Emily Johnson',
        contactNo: '+91-3123445434',
        Experience: "since 2022",
        carnumber: "UP-1234",
        completedTrips: 18,
        email: 'emily.johnson@example.com',
        rating: 5,
        encryptedTrips: 'TRP-3456-DEF',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 4,
        name: 'Michael Brown',
        contactNo: '+91-3456367354',
        carnumber: "UP-1234",
        completedTrips: 10,
        Experience: "since 2022",
        email: 'michael.brown@example.com',
        rating: 3,
        encryptedTrips: 'TRP-4567-GHI',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 5,
        name: 'Sarah Wilson',
        contactNo: '+91-9876543210',
        carnumber: "UP-5678",
        completedTrips: 22,
        Experience: "since 2021",
        email: 'sarah.wilson@example.com',
        rating: 4,
        encryptedTrips: 'TRP-5678-JKL',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 6,
        name: 'Robert Davis',
        contactNo: '+91-5551234567',
        carnumber: "UP-9012",
        completedTrips: 15,
        Experience: "since 2023",
        email: 'robert.davis@example.com',
        rating: 5,
        encryptedTrips: 'TRP-6789-MNO',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 7,
        name: 'Jennifer Martinez',
        contactNo: '+91-1112223333',
        carnumber: "UP-3456",
        completedTrips: 30,
        Experience: "since 2020",
        email: 'jennifer.martinez@example.com',
        rating: 4,
        encryptedTrips: 'TRP-7890-PQR',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 8,
        name: 'David Thompson',
        contactNo: '+91-4445556666',
        carnumber: "UP-7890",
        completedTrips: 19,
        Experience: "since 2022",
        email: 'david.thompson@example.com',
        rating: 3,
        encryptedTrips: 'TRP-8901-STU',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 9,
        name: 'Jessica Taylor',
        contactNo: '+91-7778889999',
        carnumber: "UP-2345",
        completedTrips: 25,
        Experience: "since 2021",
        email: 'jessica.taylor@example.com',
        rating: 5,
        encryptedTrips: 'TRP-9012-VWX',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
    {
        id: 10,
        name: 'James Anderson',
        contactNo: '+91-2223334444',
        carnumber: "UP-6789",
        completedTrips: 12,
        Experience: "since 2023",
        email: 'james.anderson@example.com',
        rating: 4,
        encryptedTrips: 'TRP-0123-YZA',
        image: "https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZHJpdmVyfGVufDB8fDB8fHww"
    },
];

const assignments = [
    { vehicleId: 1, driverId: 1 },
    { vehicleId: 2, driverId: 2 },
    { vehicleId: 3, driverId: 3 },
    { vehicleId: 4, driverId: 4 },
    { vehicleId: 5, driverId: 5 },
    { vehicleId: 6, driverId: 6 },
    { vehicleId: 7, driverId: 7 },
    { vehicleId: 8, driverId: 8 },
    { vehicleId: 9, driverId: 9 },
    { vehicleId: 10, driverId: 10 },
];

// Function to generate star rating HTML
function generateStarRating(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-yellow-500"></i>';
        } else {
            stars += '<i class="far fa-star text-yellow-500"></i>';
        }
    }
    return stars;
}

// Function to show the summary
function showSummary() {
    const summaryContent = document.getElementById('summaryContent');

    if (!summaryContent) {
        console.error('Summary content container not found');
        return;
    }

    summaryContent.innerHTML = '';

    if (assignments.length === 0) {
        summaryContent.innerHTML = '<p class="text-center text-gray-500 py-6">No vehicles selected</p>';
        return;
    }

    // Create a container for the assignments
    const assignmentsContainer = document.createElement('div');
    assignmentsContainer.className = 'row';

    assignments.forEach((assignment, index) => {
        const vehicle = vehicles.find(v => v.id === assignment.vehicleId);
        const driver = drivers.find(d => d.id === assignment.driverId);

        if (!vehicle || !driver) {
            console.error(`Vehicle or driver not found for assignment #${index + 1}`);
            return;
        }

        const cardWrapper = document.createElement('div');
        cardWrapper.className = 'col-lg-6 mb-4'; // 2 per row with spacing

        const card = document.createElement('div');
        card.className = 'rounded-xl border border-black-200 hover:shadow-xl bg-white h-full transition-shadow duration-300';
        card.innerHTML = `
    <div class="p-4 rounded-lg h-full shadow">
        <!-- Mobile view: stack vertically -->
        <div class="flex flex-col sm:flex-row md:items-center">
            <!-- Image - full width on mobile, limited width on desktop -->
            <div class="w-full sm:w-20 h-48 sm:h-20 bg-white rounded shadow-sm p-1 mb-4 md:mb-0 md:mr-4">
                <img src="${vehicle.image}" alt="${vehicle.name}" class="w-full h-full object-cover rounded">
            </div>
            
            <!-- Content section - stacks on mobile, side by side on desktop -->
            <div class="flex flex-col sm:ps-2 sm:flex-row w-full">
                <!-- Vehicle info -->
                <div class="flex-grow mb-3 md:mb-0">
                    <h6 class="font-bold">${vehicle.name} <span class="text-black text-2xl font-bold">X${vehicle.count}</span></h6>
                    <p class="text-gray-500">${vehicle.type} - ${vehicle.people} People</p>
                    
                </div>
                
                <!-- Driver info -->
                <div class="md:ml-auto text-left md:text-right">
                    <p class="font-bold">${driver.name}</p>
                    <p class="text-gray-500">${driver.contactNo}</p>
                    <div class="rating">
                        ${Array(driver.rating).fill('<i class="fas fa-star text-orange-500"></i>').join('')}
                    </div>
                </div>
            </div>
        </div>
    </div>
`;

        cardWrapper.appendChild(card);
        assignmentsContainer.appendChild(cardWrapper);

    });

    summaryContent.appendChild(assignmentsContainer);
}

// Handle finish button
document.addEventListener('DOMContentLoaded', function () {
    // Display the summary when the page loads
    showSummary();

});
// book-now modal function code : start
// Handling the back button function(otp veri.) - code
const initBackButtonHandler = () => {
    let from = 'user';
    $('label[for="agent"]').click(() => from = 'agent');
    $('label[for="step1"]').click(() => from = 'user');
    $('#back-btn').click(() => {
        $('#' + (from === 'agent' ? 'agent' : 'step1')).prop('checked', true);
    });
};
// Handling the Book Now button function - code
const initBookNowButtonHandler = () => {
    const bookNowBtn = document.getElementById("book-now-btn");
    const modalToggle = document.getElementById("modal-toggle");
    if (bookNowBtn && modalToggle) {
        bookNowBtn.addEventListener("click", function () {
            modalToggle.checked = true;
        });
    }
};