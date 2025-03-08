import {useEffect} from "react";
import toastr from "toastr";
import {usePage} from "@inertiajs/react";

toastr.options.timeOut = 500;

function useToastr() {
    const props = usePage().props;
    const {flash: {successMessage, errorMessage}} = props;

    useEffect(() => {
        if (successMessage) {
            toastr.success(successMessage);
        }
        if (errorMessage) {
            toastr.error(errorMessage);
        }
    }, [props]);

    return null;
}

export default useToastr;
