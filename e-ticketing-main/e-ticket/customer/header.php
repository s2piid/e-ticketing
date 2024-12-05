<?php
if (!isset($step)) {
    $step = 1; // Default to step 1
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title><?= isset($pageTitle) ? $pageTitle : "Ferry Booking System" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --background-color: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-radius: 1rem;
        }

        body {
            background: linear-gradient(135deg, #f6f8fb 0%, #e9eef5 100%);
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .booking-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 3rem 2rem;
            border-radius: var(--border-radius);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .booking-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="white" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.1;
            animation: wave 10s linear infinite;
        }

        .booking-header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
        }

        .booking-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            position: relative;
        }

        .step-indicator {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 3rem;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 0 1rem;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 2.5rem;
            right: -50%;
            width: 100%;
            height: 3px;
            background: #e5e7eb;
            z-index: 1;
        }

        .step.active:not(:last-child)::after {
            background: var(--primary-color);
        }

        .step-number {
            width: 3rem;
            height: 3rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.2);
        }

        .step div:last-child {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .step.active div:last-child {
            color: var(--primary-color);
            font-weight: 600;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            50% { transform: translateX(-25%); }
            100% { transform: translateX(0); }
        }

        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1.5rem;
                padding: 2rem 1rem;
            }

            .step:not(:last-child)::after {
                top: auto;
                right: auto;
                bottom: -1.5rem;
                left: 50%;
                width: 3px;
                height: 1.5rem;
                transform: translateX(-50%);
            }

            .step {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 0;
            }

            .step-number {
                margin: 0;
            }
        }

        /* Add smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Add loading animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading::after {
            content: '';
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .booking-review {
        background: #f8f9fa;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .booking-header {
        background: #0d6efd;
        color: white;
        padding: 20px;
        border-radius: 15px 15px 0 0;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
    }

    .passenger-card {
        background: white;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        transition: transform 0.2s;
    }

    .passenger-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .info-label {
        color: #6c757d;
        font-weight: 600;
        min-width: 120px;
        display: inline-block;
    }

    .price-tag {
        background: #198754;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: bold;
    }

    .discount-badge {
        background: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 0.8em;
    }

    .action-buttons {
        gap: 10px;
    }

    .action-buttons .btn {
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 500;
    }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="booking-header">
            <h1><i class="fas fa-ship me-2"></i>Ferry E-Ticketing</h1>
            <p>Experience seamless journey booking with our modern platform</p>
        </header>
        <nav class="step-indicator">
            <?php
            $steps = [
                1 => ['icon' => 'fa-calendar-alt', 'text' => 'Travel Details'],
                2 => ['icon' => 'fa-ship', 'text' => 'Ferry Selection'],
                3 => ['icon' => 'fa-users', 'text' => 'Passenger Details'],
                4 => ['icon' => 'fa-clipboard-check', 'text' => 'Review Booking'],
                5 => ['icon' => 'fa-credit-card', 'text' => 'Payment']
            ];
            
            foreach ($steps as $stepNum => $stepInfo): ?>
                <div class="step <?= $step >= $stepNum ? 'active' : '' ?>">
                    <div class="step-number">
                        <i class="fas <?= $stepInfo['icon'] ?>"></i>
                    </div>
                    <div><?= $stepInfo['text'] ?></div>
                </div>
            <?php endforeach; ?>
        </nav>

        <!-- Loading animation div -->
        <div class="loading" style="display: none;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show loading animation when navigating
        $(document).on('click', 'a, button[type="submit"]', function() {
            $('.loading').fadeIn();
        });

        // Hide loading animation when page is fully loaded
        $(window).on('load', function() {
            $('.loading').fadeOut();
        });
    </script>
</body>
</html>