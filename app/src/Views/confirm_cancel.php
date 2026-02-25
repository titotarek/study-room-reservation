<?php
/**
 * @var int $reservationId
 */

$title = 'Confirm Cancellation';

require __DIR__ . '/partials/header.php';
?>

<main class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-6 sm:p-8 text-center border-t-8 border-red-500">

        <div
            class="w-16 h-16 sm:w-20 sm:h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl sm:text-4xl font-black">
            !
        </div>

        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">
            Are you sure?
        </h2>

        <p class="text-sm sm:text-base text-gray-600 mb-8">
            Do you really want to cancel this booking? This action cannot be undone.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <a href="/my-reservations"
               class="order-2 sm:order-1 flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition text-center">
                No, Keep it
            </a>

            <a href="/reservations/cancel?id=<?= $reservationId ?>&confirm=1"
               class="order-1 sm:order-2 flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200 text-center">
                Yes, Cancel
            </a>
        </div>
    </div>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
