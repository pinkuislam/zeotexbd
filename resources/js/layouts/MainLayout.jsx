
import { Head } from "@inertiajs/react";
import Footer from "../components/Footer";
import NavbarComponent from "../components/NavbarComponent";

const MainLayout = (props) => {
    const {config} = props.children.props;
    return (
        <>
            {/* <Head>
                <title>{config.site_settings.meta_title || config.app.name}</title>
                <meta name="description" content={config.site_settings.meta_description} />
                <meta name="keywords" content={config.site_settings.meta_keywords} />
            </Head> */}
            <section className="">
                <NavbarComponent {...props.children.props} />
                <div className="overflow-hidden">
                    {props.children}
                </div>
                <Footer {...props.children.props} />
            </section>
        </>
    )
}

export default MainLayout;
