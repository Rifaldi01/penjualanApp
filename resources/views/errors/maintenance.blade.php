<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Maintenance</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{URL::to('assets/images/asd.png')}}" type="image/png')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

    <style>

        body {

            margin: 0;
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: linear-gradient(135deg, #1f2937, #0f172a, #111827);

            overflow: hidden;

            color: white;

            font-family: Segoe UI;

        }

        .card-maintenance {

            width: 650px;

            border: none;

            border-radius: 25px;

            background: rgba(255, 255, 255, .08);

            backdrop-filter: blur(15px);

            padding: 50px;

            text-align: center;

            box-shadow: 0 20px 60px rgba(0, 0, 0, .4);

            animation: fadeIn 1s;

        }

        .gear {

            font-size: 90px;

            color: #ffc107;

            animation: spin 6s linear infinite;

        }

        h1 {

            margin-top: 20px;

            font-weight: bold;

        }

        p {

            color: #d7d7d7;

            margin-top: 15px;

            font-size: 18px;

        }

        .progress {

            height: 15px;

            border-radius: 30px;

            overflow: hidden;

            margin-top: 40px;

        }

        .progress-bar {

            width: 0;

            animation: loading 12s linear infinite;

        }

        .count {

            font-size: 35px;

            font-weight: bold;

            color: #ffc107;

            margin-top: 20px;

        }

        @keyframes spin {

            from {

                transform: rotate(0deg);

            }

            to {

                transform: rotate(360deg);

            }

        }

        @keyframes loading {

            from {

                width: 0%;

            }

            to {

                width: 100%;

            }

        }

        @keyframes fadeIn {

            from {

                opacity: 0;

                transform: translateY(40px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }

        .dot {

            position: absolute;

            width: 10px;

            height: 10px;

            background: white;

            border-radius: 50%;

            opacity: .3;

            animation: float 8s linear infinite;

        }

        @keyframes float {

            from {

                transform: translateY(100vh);

            }

            to {

                transform: translateY(-100px);

            }

        }

    </style>

</head>

<body>

@for($i=0;$i<40;$i++)

    <div class="dot"
         style="
        left:{{ rand(1,100) }}%;
        animation-delay:{{ rand(1,8) }}s;
        animation-duration:{{ rand(5,15) }}s;
">
    </div>

@endfor


<div class="card-maintenance">

    <i class="fas fa-gears gear"></i>

    <h1>System Maintenance</h1>

    <p>

        Kami sedang melakukan peningkatan sistem agar memberikan pelayanan yang lebih baik.

    </p>

    <div class="progress mt-4">

        <div class="progress-bar bg-warning"></div>

    </div>

    <div class="count" id="countdown">
        😁
    </div>

    <small>

        Hormat kami, <a href="https://satyasoftware.id/">Satya Software Media</a>

    </small>

</div>

<script>


    setInterval(function () {

        second--;

        document.getElementById("countdown").innerHTML = second;


    }, 1000);

</script>

</body>
</html>
