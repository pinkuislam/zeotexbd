import useToastr from "../hooks/useToastr";

const PageWrapper = (props) => {
    useToastr();

    return props.children;
}

export default PageWrapper;
