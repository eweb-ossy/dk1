function render(users, statusViewFlag) {
    const inUsers = users.filter(elem => elem.in_flag === '1' && elem.out_flag === '0');
    const outUsers = users.filter(elem => elem.in_flag === '1' && elem.out_flag === '1');
    const $inUserDataElem = $('#inUserData');
    const $outUserDataElem = $('#outUserData');
    $inUserDataElem.html('');
    $outUserDataElem.html('');
    if (statusViewFlag === 0) {
        inUsers.sort((a, b) => {
            if (a.in_time < b.in_time) return -1;
            if (a.in_time > b.in_time) return 1;
            return 0;
        });
        outUsers.sort((a, b) => {
            if (a.out_time < b.out_time) return -1;
            if (a.out_time > b.out_time) return 1;
            return 0;
        });
        inUsers.forEach(user => {
            if (user.in_time) {
                $inUserDataElem.prepend(`<li>${user.in_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
            if (!user.in_time && user.in_work_time) {
                $inUserDataElem.prepend(`<li>${user.in_work_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
        });
        outUsers.forEach(user => {
            if (user.out_time) {
                $outUserDataElem.prepend(`<li>${user.out_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
            if (!user.out_time && user.out_work_time) {
                $outUserDataElem.prepend(`<li>${user.out_work_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
        });
    }
    if (statusViewFlag === 1) {
        inUsers.sort((a, b) => {
            if (a.in_work_time < b.in_work_time) return -1;
            if (a.in_work_time > b.in_work_time) return 1;
            return 0;
        });
        outUsers.sort((a, b) => {
            if (a.out_work_time < b.out_work_time) return -1;
            if (a.out_work_time > b.out_work_time) return 1;
            return 0;
        });
        inUsers.forEach(user => {
            if (user.in_work_time) {
                $inUserDataElem.prepend(`<li>${user.in_work_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
        });
        outUsers.forEach(user => {
            if (user.out_work_time) {
                $outUserDataElem.prepend(`<li>${user.out_work_time.substr(0, 5)} ${user.name_sei} ${user.name_mei}</li>`);
            }
        });
    }
}

export function viewUsersStatus(users, statusViewFlag) {
    render(users, statusViewFlag);
}