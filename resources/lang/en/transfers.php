<?php

return [
    'form' => [
        'from_label' => 'Pickup location',
        'select_pickup_option' => 'Select a pickup location',
        'no_pickups_available' => 'No pickup locations have been configured yet.',
        'to_label' => 'Drop-off location',
        'to_placeholder' => 'Enter a drop-off address',
    ],
    'search' => [
        'route_summary' => 'Route: :from → :to',
        'estimated_distance' => 'Approx. distance: :km km',
    ],
    'booking' => [
        'route_label' => 'Route',
        'pickup_label' => 'Pickup',
        'dropoff_label' => 'Drop-off',
        'address_label' => 'Address:',
        'datetime_label' => 'Transfer time',
        'timezone_suffix' => ' (Asia/Tbilisi)',
        'distance_label' => 'Distance',
        'pickup_required' => 'Please choose a pickup location.',
        'dropoff_required' => 'Please choose a drop-off location.',
        'invalid_pickup_location' => 'The selected pickup location is no longer available.',
        'missing_dropoff' => 'Please choose a drop-off location.',
        'distance_error' => 'Unable to calculate the travel distance for the selected locations.',
        'unavailable_pickup' => 'This vehicle is not available for the chosen pickup location.',
    ],
    'admin' => [
        'pickups' => [
            'title' => 'Pickup locations',
            'instructions' => 'Place a pin on the map and fill in the details to add a pickup location for this vehicle.',
            'form' => [
                'name' => 'Location name',
                'name_placeholder' => 'e.g. Tbilisi International Airport',
                'address' => 'Address (optional)',
                'address_placeholder' => 'Street, city, country',
                'base_price' => 'Base price',
                'coefficient' => 'Price coefficient',
                'coordinates' => 'Coordinates',
                'coordinates_hint' => 'Click the map to set latitude and longitude',
                'add_button' => 'Add pickup location',
                'reset_button' => 'Reset form',
                'validation_error' => 'Please provide a name and choose coordinates on the map before adding a pickup location.',
            ],
            'table' => [
                'name' => 'Name',
                'address' => 'Address',
                'base_price' => 'Base price',
                'coefficient' => 'Coefficient',
                'coordinates' => 'Coordinates',
                'no_coordinates' => 'Coordinates not set',
            ],
            'actions' => [
                'set_coordinates' => 'Set on map',
                'remove' => 'Remove',
                'confirm_remove' => 'Remove this pickup location?',
                'click_map_to_set' => 'Click on the map to update the coordinates for this location.',
            ],
        ],
    ],
];
