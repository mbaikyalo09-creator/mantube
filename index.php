<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Finaswift STK Push Demo</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: "#197fe6"
      },
      fontFamily: {
        display: ["Manrope", "sans-serif"]
      }
    }
  }
}
</script>
</head>

<body class="bg-slate-100 font-display min-h-screen flex items-center justify-center">

<!-- PAYMENT CARD -->
<div class="w-full max-w-[480px] bg-white rounded-2xl shadow-xl p-8">

<!-- FORM SECTION -->
<div id="form-section" class="flex flex-col gap-6">

  <div class="text-center">
    <h1 class="text-2xl font-extrabold">Secure STK Push 🔒</h1>
    <p class="text-slate-500 text-sm mt-1">
      Enter your M-PESA details to continue
    </p>
  </div>

  <div class="space-y-4">
    <div>
      <label class="text-sm font-semibold">Phone Number</label>
      <input id="msisdn"
             class="w-full mt-1 p-4 rounded-xl border bg-slate-50"
             placeholder="07XXXXXXXX">
    </div>

    <div>
      <label class="text-sm font-semibold">Amount (KES)</label>
      <input id="amount"
             type="number"
             class="w-full mt-1 p-4 rounded-xl border bg-slate-50"
             placeholder="1000">
    </div>

    <button onclick="startPayment()"
            class="w-full mt-4 bg-primary text-white py-4 rounded-full font-bold hover:opacity-90 transition">
      Pay Now →
    </button>
  </div>

</div>

<!-- WAITING SECTION -->
<div id="waiting-section" class="hidden text-center py-10 space-y-4 animate-pulse">

  <div class="mx-auto w-14 h-14 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>

  <h2 class="text-xl font-bold">Check your phone 📱</h2>
  <p class="text-slate-500">
    An STK push has been sent. Enter your M-PESA PIN to complete payment.
  </p>

</div>

<!-- RESULT SECTION -->
<div id="result-section" class="hidden text-center space-y-4 py-6">

  <h2 id="result-title" class="text-2xl font-bold"></h2>
  <p id="result-details" class="text-slate-600"></p>

  <button onclick="location.reload()"
          class="mt-6 px-6 py-3 bg-primary text-white rounded-full font-semibold">
    Make Another Payment
  </button>

</div>

</div>

<!-- ORIGINAL LOGIC (UNCHANGED) -->
<script>
function startPayment() {
    const msisdn = document.getElementById("msisdn").value;
    const amount = document.getElementById("amount").value;

    document.getElementById("form-section").classList.add("hidden");
    document.getElementById("waiting-section").classList.remove("hidden");

    fetch("pay.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ msisdn, amount })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.transaction_request_id) {
            alert("Could not start payment");
            location.reload();
            return;
        }
        pollStatus(data.transaction_request_id);
    });
}

function pollStatus(requestId) {
    const interval = setInterval(() => {
        fetch("verify.php?id=" + requestId)
        .then(res => res.json())
        .then(data => {
            const status = data.TransactionStatus;
            const desc = data.ResultDesc;

            if (status === "Completed") {
                clearInterval(interval);
                showResult(
                    "Payment Successful 🎉",
                    "Receipt: " + data.TransactionReceipt
                );
            } else if (
                status === "Failed" ||
                desc?.includes("Cancelled") ||
                desc?.includes("Failed")
            ) {
                clearInterval(interval);
                showResult(
                    "Payment Failed ❌",
                    "Your payment did not go through."
                );
            }
        });
    }, 4000);
}

function showResult(title, details) {
    document.getElementById("waiting-section").classList.add("hidden");
    document.getElementById("result-section").classList.remove("hidden");
    document.getElementById("result-title").innerText = title;
    document.getElementById("result-details").innerText = details;
}
</script>

</body>
</html>
