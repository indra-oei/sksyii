/**
 * Format number into currency format (1.000, 10.000, and so on)
 *
 * @param {*} value
 * @returns Formatted value
 */
function formatCurrency(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const showAlert = async (response, success_message, href) => {
    if (response.hasOwnProperty("errNum")) {
        if (response.errNum == 0) {
            Swal.fire({
                title: "Success",
                text: "" + success_message,
                icon: "success",
                showConfirmButton: false,
                timer: 1500,
            }).then(() => {
                if (href) {
                    window.location.href = href;
                } else {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                title: "Wait",
                text: "" + response.errStr,
                icon: "error",
                showConfirmButton: false,
                timer: 1500,
            });
        }
    } else {
        let errMsg = Object.values(response);
        Swal.fire({
            title: "Wait",
            text: "" + errMsg[0],
            icon: "error",
            showConfirmButton: false,
            timer: 1500,
        });
    }
};
