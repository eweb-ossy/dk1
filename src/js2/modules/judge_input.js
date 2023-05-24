function judge(inFlag, outFlag, restFlag, autoRest) {
    if (!inFlag || inFlag === 0) {
        $('#input_btn').removeClass('disable');
        $('#output_btn').addClass('disable');
        $('.rest-bloc').addClass('disable');
        $('#nonstop_in').removeClass('disable');
        $('#nonstop_out').addClass('disable');
    }
    if (inFlag === 1 && outFlag === 0) {
        $('#input_btn').addClass('disable');
        $('#output_btn').removeClass('disable');
        $('#nonstop_in').addClass('disable');
        $('#nonstop_out').removeClass('disable');
        if (restFlag !== 3 && !autoRest) {
            $('.rest-bloc').addClass('disable');
            if (restFlag === '' || restFlag === 1) {
                $('#rest_in_btn').removeClass('disable');
            }
            if (restFlag === 0) {
                $('#rest_out_btn').removeClass('disable');
                $('#output_btn').addClass('disable');
            }
        }
    }
    if (inFlag === 1 && outFlag === 1) {
        $('#input_btn').addClass('disable');
        $('#output_btn').addClass('disable');
        $('.rest-bloc').addClass('disable');
        $('#nonstop_in').addClass('disable');
        $('#nonstop_out').addClass('disable');
    }
}

export function judgeInput(inFlag, outFlag, restFlag, autoRest) {
    judge(inFlag, outFlag, restFlag, autoRest);
}