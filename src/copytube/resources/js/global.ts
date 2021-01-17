/**
 * format: yyyy-mm-dd
 */
export function getCurrentDate() {
    const today: Date = new Date();
    const year = today.getFullYear();
    const month =
        today.getMonth() + 1 > 9
            ? today.getMonth() + 1
            : "0" + (today.getMonth() + 1);
    const day = today.getDate();
    const date: string = year + "-" + month + "-" + day;
    return date;
}
