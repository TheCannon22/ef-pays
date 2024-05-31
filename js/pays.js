jQuery(document).ready(function($) {
    const pays = ["France", "États-Unis", "Canada", "Argentine", "Chili", "Belgique", "Maroc", "Mexique", "Japon", "Italie", "Islande", "Chine", "Grèce", "Suisse"];
    const conteneurDesPays = $('#country-menu');
    const conteneurDestinations = $('#country-destinations');

    // Generate country menu
    pays.forEach(country => {
        conteneurDesPays.append(`<button class="country-btn" data-country="${country}">${country}</button>`);
    });

    // Load default country destinations (France)
    loadDestinations('France');

    // Add click event to country buttons
    $('.country-btn').on('click', function() {
        const country = $(this).data('country');
        loadDestinations(country);
    });

    function loadDestinations(country) {
        $.ajax({
            url: `${paysApi.root}pays/v1/destinations`,
            method: 'GET',
            data: { country },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', paysApi.nonce);
            },
            success: function(response) {
                conteneurDestinations.empty();
                response.forEach(destination => {
                    conteneurDestinations.append(`
                        <div class="destination">
                            <h3>${destination.title}</h3>
                            <img src="${destination.image}" alt="${destination.title}">
                            <p>${destination.content}</p>
                        </div>
                    `);
                });
            }
        });
    }
});
