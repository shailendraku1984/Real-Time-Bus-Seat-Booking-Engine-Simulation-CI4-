<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm border-0 rounded-4">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    Bus Seat Layout
                </h3>

                <p class="text-muted mb-0">
                    Real-Time Seat Booking Simulation
                </p>
            </div>

            <div>
                <span class="badge bg-success">Available</span>
                <span class="badge bg-danger">Booked</span>
                <span class="badge bg-primary">Selected</span>
            </div>

        </div>

        <div class="row g-3 mb-4">

            <?php foreach ($seats as $seat): ?>

                <?php

                    $seatClass = 'seat-available';

                    if ($seat['status'] === 'booked') {
                        $seatClass = 'seat-booked';
                    }

                ?>

                <div class="col-3">

                    <div
                        class="seat <?= $seatClass ?>"
                        data-seat-id="<?= $seat['id'] ?>"
                        data-seat-status="<?= $seat['status'] ?>"
                    >
                        <?= esc($seat['seat_no']) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <!-- Booking Panel -->

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h5 class="fw-bold mb-1">
                            Selected Seat
                        </h5>

                        <p class="mb-0 text-muted" id="selectedSeatText">
                            No seat selected
                        </p>

                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <button
                            class="btn btn-primary px-4"
                            id="bookNowBtn"
                            disabled
                        >
                            <i class="bi bi-ticket-perforated"></i>
                            Book Now
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
  
    let selectedSeatId = null;
    let selectedSeatElement = null;

    const seats =
        document.querySelectorAll('.seat');

    const selectedSeatText =
        document.getElementById('selectedSeatText');

    const bookNowBtn =
        document.getElementById('bookNowBtn');

    // Seat click
    seats.forEach(seat => {

        seat.addEventListener('click', function () {

            const status =
                this.dataset.seatStatus;

            // Prevent booked seat
            if (status === 'booked') {
                return;
            }

            // Remove previous selection
            if (selectedSeatElement) {

                selectedSeatElement.classList.remove(
                    'seat-selected'
                );

                selectedSeatElement.classList.add(
                    'seat-available'
                );
            }

            selectedSeatElement = this;

            selectedSeatId =
                this.dataset.seatId;

            this.classList.remove(
                'seat-available'
            );

            this.classList.add(
                'seat-selected'
            );

            selectedSeatText.innerHTML =
                'Seat Selected: <strong>' +
                this.innerText +
                '</strong>';

            bookNowBtn.disabled = false;

        });

    });

    // Book Now Click
    bookNowBtn.addEventListener('click', function () {

        if (!selectedSeatId) {
            return;
        }

        // Disable button during request
        bookNowBtn.disabled = true;

        bookNowBtn.innerHTML =
            'Processing...';

        const csrfToken =
            document.querySelector(
                'meta[name=\"csrf-token\"]'
            ).content;

        fetch('<?=base_url()?>book-seat', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken
            },

            body:
                'seat_id=' + selectedSeatId

        })

        .then(response => response.json())

        .then(data => {

            if (data.status) {

                // Update UI
                selectedSeatElement.classList.remove(
                    'seat-selected'
                );

                selectedSeatElement.classList.add(
                    'seat-booked'
                );

                selectedSeatElement.dataset.seatStatus =
                    'booked';

                selectedSeatText.innerHTML =
    '<span class="text-success">' +
    data.message +
    '<br>' +
    'Booking No: <strong>' +
    data.booking_no +
    '</strong>' +
    '</span>';

            } else {

                selectedSeatText.innerHTML =
    '<span class="text-success">' +
    data.message +
    '<br>' +
    'Booking No: <strong>' +
    data.booking_no +
    '</strong>' +
    '</span>';
            }

            selectedSeatId = null;

            selectedSeatElement = null;

            bookNowBtn.innerHTML =
                'Book Now';

        })

        .catch(error => {

            console.error(error);

            selectedSeatText.innerHTML =
                '<span class=\"text-danger\">Booking failed</span>';

            bookNowBtn.innerHTML =
                'Book Now';
        });

    });

</script>
 

<?= $this->endSection() ?>