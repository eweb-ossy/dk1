import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja";
import {TabulatorFull as Tabulator} from 'tabulator-tables';

const datepicker = flatpickr('#datepicker', {
    locale: Japanese,
    dateFormat: 'Y年m月d日(D)',
    ariaDateFormat: 'Y-m-d,w',
    defaultDate: 'today',
    onDayCreate: (dObj, dStr, fp, dayElem)=> {
        const dateData = dayElem.getAttribute('aria-label');
        const date = dateData.split(',')[0]; // YYYY-MM-DD
        const week = dateData.split(',')[1]; // 0-6
        const className = week == 0 ? 'sun' : week == 6 ? 'sat' : '';
        if (className) dayElem.classList.add(className);
    }
});

// $.ajax({
//     url: '../data/admin_user_list/getData',
//     dataType: 'json'
// }).done(function(tableData) {
//     console.log(tableData);
// });