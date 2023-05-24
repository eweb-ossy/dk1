const renderTime = () => {
    const $dateViewElem = $('#date_view');
    const $timeViewElem = $('#time_view');
    const $secViewElem = $('#second');
    const countDown = () => {
        const nowDataTime = new Date();
        const year = nowDataTime.getFullYear();
        const month = (nowDataTime.getMonth() + 1).toString().padStart(2, '0');
        const day = nowDataTime.getDate().toString().padStart(2, '0');
        const weekStr = ['日', '月', '火', '水', '木', '金', '土'][nowDataTime.getDay()];
        const h = nowDataTime.getHours().toString().padStart(2, '0');
        const m = nowDataTime.getMinutes().toString().padStart(2, '0');
        const s = nowDataTime.getSeconds().toString().padStart(2, '0');
        $dateViewElem.text(`${year}年${month}月${day}日(${weekStr})`);
        const msec = nowDataTime.getMilliseconds();
        const colon = msec > 499 ? " " : ":";
        $timeViewElem.text(`${h}${colon}${m}`);
        $secViewElem.text(s);
        setTimeout(countDown, 500 - m % 500);
    }
    countDown();
}

export function renderClock() {
    renderTime();
}