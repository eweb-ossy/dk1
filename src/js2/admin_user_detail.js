import Cookies from 'js-cookie';

(() => {

    const user_id = Cookies.get('userDetailUserId') ? Cookies.get('userDetailUserId') : 'new';
    const key = Cookies.get('userDetailTabKey') && user_id !== 'new' ? Cookies.get('userDetailTabKey') : '01';

    

})();