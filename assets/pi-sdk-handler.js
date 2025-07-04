// pi-sdk-handler.js

document.addEventListener("DOMContentLoaded", async () => {
    if (!window.Pi) {
        console.error("Pi SDK not loaded.");
        return;
    }

    Pi.init({
        version: "2.0",
        sandbox: piAjax.sandbox_mode,
    });

    function onIncompletePaymentFound(payment) {
        console.log("Incomplete payment:", payment);
    }

    // 🔐 Login button only
    const loginBtn = document.querySelector("#pi-login-btn");
    if (loginBtn) {
        loginBtn.addEventListener("click", async () => {
            try {
                const scopes = ["username"];
                const user = await Pi.authenticate(scopes, onIncompletePaymentFound);

                // Send username to backend
                fetch(piAjax.ajax_url, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        action: "pi_save_user",
                        username: user.username
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Welcome, ${user.username}!`);
                            window.location.href = data.data.redirect;
                        } else {
                            console.error("Login error:", data);
                        }
                    });
            } catch (err) {
                console.error("Login failed:", err);
            }
        });
    }

    // 💸 Login & Pay button
    const loginPayBtn = document.querySelector("#pi-login-pay-btn");
    if (loginPayBtn) {
        loginPayBtn.addEventListener("click", async () => {
            // 🌐 Environment Check
            if (piAjax.sandbox_mode) {
                console.log("🚧 Running in SANDBOX mode");
            } else {
                console.log("🟢 Running in LIVE mode");
            }

            try {
                const scopes = ["username"];
                const user = await Pi.authenticate(scopes, onIncompletePaymentFound);

                // Send username to backend
                await fetch(piAjax.ajax_url, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        action: "pi_save_user",
                        username: user.username
                    })
                });

                // Trigger payment
                const payment = await Pi.createPayment({
                    amount: piAjax.payment_amount || 1,
                    memo: piAjax.payment_memo || "Default memo",
                    metadata: { plugin: "pi-wp" }
                }, {
                    onReadyForServerApproval: (paymentId) => {
                        console.log("Ready for approval:", paymentId);
                    },
                    onReadyForServerCompletion: (paymentId, txid) => {
                        console.log("Ready for completion:", paymentId, txid);

                        // Verify payment server-side
                        fetch(piAjax.ajax_url, {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: new URLSearchParams({
                                action: "pi_verify_payment",
                                payment_id: paymentId
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert("✅ Payment verified!");
                                } else {
                                    alert("❌ Payment verification failed: " + data.data);
                                }
                            })
                            .catch(err => {
                                console.error("AJAX error:", err);
                                alert("Something went wrong while verifying the payment.");
                            });
                    },
                    onCancel: (paymentId) => {
                        console.warn("Payment cancelled:", paymentId);
                    },
                    onError: (error, paymentId) => {
                        console.error("Payment error:", error);
                    }
                });

                console.log("Payment created:", payment);
            } catch (err) {
                console.error("Login or payment failed:", err);
            }
        });
    }
});
