import Slider from "react-slick";
import useZiggy from "../hooks/useZiggy";
import PageWrapper from "../layouts/PageWrapper";
import CategoryProduct from "../components/CategoryProduct";
import { Head, Link } from "@inertiajs/react";
// import { set } from "vue/types/umd";
import { Menu, MenuItem, MenuButton, SubMenu, ControlledMenu, useHover, useMenuState, useClick } from "@szhsin/react-menu";
import "@szhsin/react-menu/dist/index.css";
import "@szhsin/react-menu/dist/transitions/slide.css";
import { useRef, useState } from 'react';





const Home = (props) => {
    const { route } = useZiggy();
    const { new_arrival_products, best_selling_products, sliders, highlights } = props;

    const PrevArrow = ({ onClick }) => (
        <div className="custom-prev-arrow text-center" onClick={onClick}>
            <i className="bi bi-caret-left fs-5 text-light"></i>
        </div>
    );

    const NextArrow = ({ onClick }) => (
        <div className="custom-next-arrow text-center" onClick={onClick}>
            <i className="bi bi-caret-right fs-5 text-light"></i>
        </div>
    );

    const settings = {
        dots: false,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        speed: 1500,
        autoplaySpeed: 4000,
        loop: true,
        prevArrow: <PrevArrow />,
        nextArrow: <NextArrow />,
    };


    // const anchorPoint = { x: 0, y: 0 };


    return (
        <PageWrapper>
            <Head>
                {props.config.site_settings.meta_title && <title>{props.config.site_settings.meta_title}</title>}
                {props.config.site_settings.meta_description && <meta name="description" content={props.config.site_settings.meta_description} />}
                {props.config.site_settings.meta_keywords && <meta name="keywords" content={props.config.site_settings.meta_keywords} />}
            </Head>
            <section className="main-container overflow-hidden mt-3">
                <div className="row overflow-hidden banner-main-container">
                    <div className="col-md-2 d-md-block d-none px-2">
                        <div className="d-flex flex-column justify-content-between align-items-between small-img-container">
                            {/* {
                                highlights.slice(0, 2).map(hi => (
                                    <a key={hi.id} href={hi.link} target={hi.is_new_tab == 'Yes' ? "_blank" : ""}>
                                        <img className="w-100 banner-small-img" src={hi.image_url} alt={hi.title} />
                                    </a>
                                ))
                            } */}

                            {/* <>
                                <div className="w-100">
                                    <ControlledMenu
                                        state="open"
                                        anchorPoint={anchorPoint}
                                        transition={true}
                                    >
                                        {props.navbar_categories.map((category) => (
                                            <div key={category.id}>
                                                <SubMenu state="open" align="center" label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", category.slug)}>{category.name}</Link>}>
                                                    {category.childs.length > 0 && (
                                                        < >
                                                            {category.childs.map((subcategory) => (
                                                                <div key={subcategory.id}>
                                                                    <SubMenu state="open" align="center" label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", subcategory.slug)}>{subcategory.name}</Link>}>
                                                                        <>
                                                                            {subcategory.childs.map(
                                                                                (cat) => (
                                                                                    <MenuItem key={cat.id}>
                                                                                        <Link
                                                                                            className="text-decoration-none text-dark"
                                                                                            href={route("ecommerce.category", cat.slug)}
                                                                                        >
                                                                                            {cat.name}
                                                                                        </Link>
                                                                                    </MenuItem>
                                                                                )
                                                                            )}
                                                                        </>
                                                                    </SubMenu>
                                                                </div>
                                                            )
                                                            )}
                                                        </>
                                                    )}
                                                </SubMenu >
                                            </div>
                                        ))}
                                    </ControlledMenu>
                                </div>
                            </> */}

                            <div className="primary-menu ms-4 mt-1">
                                <ul className="navbar-nav">
                                    <li className="nav-item dropdown d-md-block d-none dropdown-large">
                                        <ul className="dropdown-menu  dropdown-menu-container">
                                            {props.navbar_categories.map((category) => (
                                                <li key={category.id} className="nav-item dropdown">
                                                    <Link className="dropdown-item dropdown-toggle dropdown-toggle-nocaret d-flex justify-content-between align-items-center" href={route("ecommerce.category", category.slug)}>
                                                        {category.name}{" "}
                                                        <i className="fa-solid fa-angle-right float-end ms-2"></i>
                                                    </Link>
                                                    {category.childs.length > 0 && (
                                                        <ul className="submenu dropdown-menu">
                                                            {category.childs.map(
                                                                (subcategory) => (
                                                                    <li key={subcategory.id} className="nav-item dropdown">
                                                                        <Link
                                                                            className="text-decoration-none text-dark nav-item dropdown-item d-flex justify-content-between align-items-center"
                                                                            href={route("ecommerce.category", subcategory.slug)}
                                                                        >
                                                                            {subcategory.name}
                                                                            <i className="fa-solid fa-angle-right float-end ms-2"></i>
                                                                        </Link>

                                                                        <ul className="submenu dropdown-menu">
                                                                            {subcategory.childs.map(
                                                                                (cat) => (
                                                                                    <li key={cat.id}>
                                                                                        <Link
                                                                                            className="text-decoration-none text-dark nav-item dropdown-item"
                                                                                            href={route("ecommerce.category", cat.slug)}
                                                                                        >
                                                                                            {cat.name}
                                                                                        </Link>
                                                                                    </li>
                                                                                )
                                                                            )}
                                                                        </ul>
                                                                    </li>
                                                                )
                                                            )}
                                                        </ul>
                                                    )}
                                                </li>
                                            ))}
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-8 px-1 ">
                        <div >
                            <Slider {...settings} className="carousel-container">
                                {
                                    sliders.map(slider => (
                                        <div key={slider.id}>
                                            <img className="w-100 banner-main-img" src={slider.image_url} alt="" srcSet="" />
                                        </div>
                                    ))
                                }
                            </Slider>
                        </div>
                    </div>

                    <div className="col-md-2  d-md-block d-none px-2">
                        <div className="d-flex flex-column justify-content-between align-items-between small-img-container">
                            {
                                highlights.slice(0, 2).map(hi => (
                                    <a key={hi.id} href={hi.link} target={hi.is_new_tab == 'Yes' ? "_blank" : ""}>
                                        <img className="w-100 banner-small-img " src={hi.image_url} alt={hi.title} />
                                    </a>
                                ))
                            }
                        </div>
                    </div>
                </div>
                <CategoryProduct title="New Arrivals" products={new_arrival_products} />
                <CategoryProduct title="Best Selling Products" products={best_selling_products} />
            </section>
        </PageWrapper>
    )
}

export default Home;
