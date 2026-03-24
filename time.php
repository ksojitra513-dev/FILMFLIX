<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Booking - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Pure Black Background */
        body { 
            background-color: #000000 !important; 
            color: #ffffff; 
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header और Content के बीच का सुरक्षित गैप */
        .main-wrapper {
            padding-top: 110px; /* Header से दूरी */
            padding-bottom: 150px;
            background-color: #000000;
        }

        /* Time Section - Glassy Dark Look */
        .time-wrapper {
            background: #0a0a0a;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 50px;
            border: 1px solid #1a1a1a;
        }

        .time-slot {
            display: inline-block;
            padding: 12px 28px;
            margin: 10px;
            border: 1.5px solid #e50914;
            color: #e50914;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
            background: transparent;
        }

        .time-slot:hover, .time-slot.active {
            background: #e50914;
            color: #fff !important;
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
            transform: translateY(-3px);
        }

        /* Screen Area */
        .screen-container { perspective: 1000px; margin-bottom: 60px; }
        .cinema-screen { 
            height: 12px; width: 75%; margin: 0 auto;
            background: #ffffff; transform: rotateX(-45deg);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.25);
            border-radius: 50%;
        }

        /* Seat Styling */
        .seat { 
            width: 38px; height: 34px; 
            background: #1a1a1a; 
            border-radius: 8px 8px 4px 4px; 
            cursor: pointer; margin: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: bold;
            transition: 0.2s; border: 1px solid #252525;
            color: #555;
        }

        .seat:not(.occupied):hover {
            border-color: #e50914;
            color: #fff;
            transform: scale(1.1);
        }

        .seat.selected { 
            background: #e50914 !important; 
            color: #fff !important;
            border-color: #e50914;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.6);
        }

        .seat.occupied { 
            background: #050505 !important; 
            opacity: 0.1; 
            cursor: not-allowed; 
            border: none;
        }

        /* Bottom Sticky Bar */
        .booking-bar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #0a0a0a;
            border-top: 1px solid #1a1a1a;
            padding: 20px 0;
            z-index: 1000;
        }
.btn-confirm {
        background-color: #e50914 !important;
        border: none;
        padding: 12px 50px;
        font-weight: 800;
        letter-spacing: 1px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        cursor: pointer;
    }

    /* जब बटन एक्टिव हो (सीट सिलेक्ट होने के बाद) */
    .btn-confirm:hover:not(:disabled) {
        background-color: #ffffff !important;
        color: #e50914 !important;
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 10px 20px rgba(229, 9, 20, 0.5);
    }

    /* जब बटन डिसेबल हो */
    .btn-confirm:disabled {
        background-color: #222 !important;
        opacity: 0.5;
        cursor: not-allowed;
    }
        
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="main-wrapper">
        <div class="container">
            
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h1 class="fw-bold display-4">Evil Dead Rise</h1>
                    <p class="text-secondary fs-5 mt-2">
                        <span class="badge bg-danger">18+</span> &nbsp; Horror • 1h 36m • English
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="time-wrapper text-center shadow-lg">
                        <p class="small text-uppercase fw-bold text-secondary mb-4" style="letter-spacing: 3px;">Select Showtime</p>
                        <div class="d-flex flex-wrap justify-content-center">
                            <a href="#" class="time-slot active">10:30 AM</a>
                            <a href="#" class="time-slot">01:45 PM</a>
                            <a href="#" class="time-slot">04:30 PM</a>
                            <a href="#" class="time-slot">09:00 PM</a>
                            <a href="#" class="time-slot">11:30 PM</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <div class="screen-container">
                        <div class="cinema-screen"></div>
                        <p class="small text-secondary mt-3">SCREEN AREA</p>
                    </div>

                    <div id="seat-container" class="d-flex flex-wrap justify-content-center mx-auto" style="max-width: 600px;">
                        </div>

                    <div class="mt-5 d-flex justify-content-center gap-4 small text-secondary">
                        <div><i class="fas fa-circle text-white opacity-25"></i> Available</div>
                        <div><i class="fas fa-circle text-danger"></i> Selected</div>
                        <div><i class="fas fa-circle text-black"></i> Sold</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="booking-bar shadow-lg">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <p class="text-secondary small mb-0">Selected Seats: <span id="seat-list" class="text-white fw-bold">None</span></p>
                <h2 class="fw-bold text-white mb-0">₹<span id="price">0</span></h2>
            </div>
            <button class="btn-danger btn-confirm text-white rounded-pill shadow-lg" id="confirmBtn" disabled>
                CONFIRM & PAY
            </button>
        </div>
    </div>

    <script>
        const seatContainer = document.getElementById('seat-container');
        const seatListDisplay = document.getElementById('seat-list');
        const priceDisplay = document.getElementById('price');
        const confirmBtn = document.getElementById('confirmBtn');
        
        let selectedSeats = [];
        const occupied = [5, 6, 15, 16, 25, 26, 40, 41];

        for (let i = 1; i <= 60; i++) {
            const seat = document.createElement('div');
            seat.className = 'seat';
            seat.innerText = i;
            if (occupied.includes(i)) seat.classList.add('occupied');
            else {
                seat.onclick = () => {
                    seat.classList.toggle('selected');
                    const num = i;
                    if(seat.classList.contains('selected')) selectedSeats.push(num);
                    else selectedSeats = selectedSeats.filter(s => s !== num);
                    
                    seatListDisplay.innerText = selectedSeats.length ? selectedSeats.sort((a,b)=>a-b).join(', ') : "None";
                    priceDisplay.innerText = selectedSeats.length * 280;
                    confirmBtn.disabled = selectedSeats.length === 0;
                };
            }
            seatContainer.appendChild(seat);
        }
        // बटन क्लिक पर दूसरे पेज पर भेजने के लिए
confirmBtn.onclick = () => {
    if (selectedSeats.length > 0) {
        // यहाँ अपने दूसरे पेज का नाम लिखें (जैसे payment.php या success.php)
        window.location.href = "payment.php?seats=" + selectedSeats.join(',') + "&amount=" + (selectedSeats.length * 280);
    }
};
    </script>
</body>
</html>