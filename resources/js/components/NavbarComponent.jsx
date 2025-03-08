import { Menu, MenuItem, MenuButton, SubMenu, ControlledMenu, useHover, useMenuState } from "@szhsin/react-menu";
import "@szhsin/react-menu/dist/index.css";
import "@szhsin/react-menu/dist/transitions/slide.css";
import { Link, useForm } from "@inertiajs/react";
import useZiggy from "../hooks/useZiggy";
import MiniCartComponent from "./MiniCartComponent";
import { useRef, useState } from "react";
import { useEffect } from "react";



const NavbarComponent = (props) => {
    const { route } = useZiggy();
    const [isSticky, setIsSticky] = useState(false);

    const { get, processing, data, setData } = useForm({
        search: data,
    });

    const onSubmit = (e) => {
        e.preventDefault();
        get(route("ecommerce.search"), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 100) {
                // Change the threshold (100 in this example) as needed
                setIsSticky(true);
            } else {
                setIsSticky(false);
            }
        };
        window.addEventListener("scroll", handleScroll);

        // Cleanup the event listener when the component unmounts
        return () => {
            window.removeEventListener("scroll", handleScroll);
        };
    }, []);


    // @szhsin/react-menu
    const ref = useRef(null);
    const [menuState, toggle] = useMenuState({ transition: true });
    const { anchorProps, hoverProps } = useHover(menuState.state, toggle);


    return (
        <>
            <nav className={`navbar navbar-expand-lg bg-light primary-menu ${isSticky ? "sticky-top navbar-shadow" : ""}`}>
                <div className="container-fluid navbar-container">

                    {/* mobile-menu-category-start */}
                    <div className="d-md-none d-block">
                        <Menu transition={true} menuButton={<MenuButton className="border-0 bg-light"><i className="bi bi-list fs-2"></i></MenuButton>}>
                            <>
                                {props.navbar_categories.map((category) => (
                                    <SubMenu align="center" key={category.id} label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", category.slug)}>{category.name}</Link>}>
                                        {category.childs.length > 0 && (
                                            <>
                                                {category.childs.map((subcategory) => (
                                                    <SubMenu align="center" key={subcategory.id} label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", subcategory.slug)}>{subcategory.name}</Link>}>
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
                                                )
                                                )}
                                            </>
                                        )}
                                    </SubMenu>
                                ))}
                            </>
                        </Menu>
                    </div>
                    {/* mobile-menu-category-end */}

                    <div className="navbar-logo py-md-0 py-2">
                        <Link className="text-decoration-none" href={route("ecommerce.home")}>
                            <img className="navbar-logo-img  text-start"
                                src={props.config.site_settings.logo_url}
                                alt={props.config.app.name}
                            />
                        </Link>
                    </div>

                    <div className="d-md-none d-flex align-items-center justify-content-between py-md-0 py-2">
                        <Link href={route('ecommerce.tracking.index')} className='nav-link nav-item d-md-none d-block px-3'><i className="bi bi-truck fs-5"></i><small></small></Link>
                        <MiniCartComponent {...props} />
                    </div>

                    {/* mobile-view */}
                    <form className="d-flex justify-content-center mx-auto d-md-none d-block w-100 pb-2" role="search" >
                        <div className="d-flex justify-content-between align-items-center bg-white rounded-5 border border-primary py-0 m-0 w-100">
                            <input
                                onChange={(e) =>
                                    setData("search", e.target.value)
                                }
                                className="form-control navbar-serch-input shadow-none border-0 rounded-5 py-0"
                                type="search"
                                placeholder="Search..."
                                aria-label="Search"
                            />
                            <button onClick={onSubmit} className="btn btn border-0 sahdow-0" type="submit">
                                <i className="bi bi-search fs-6 fw-bold"></i>
                            </button>
                        </div>
                    </form>

                    {/* desktop-view */}
                    <div className="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul className="navbar-nav me-auto d-flex justify-content-center align-items-center" style={{ width: "65%" }} >
                            
                            {/* <li className="nav-item dropdown d-md-block d-none dropdown-large">
                                <a className="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                    Shop by Categories{" "}
                                    <i className="fa-solid fa-angle-down fa-md ms-1"></i>
                                </a>
                                <ul className="dropdown-menu">
                                    {props.navbar_categories.map((category) => (
                                        <li
                                            key={category.id}
                                            className="nav-item dropdown"
                                        >
                                            <Link
                                                className="dropdown-item dropdown-toggle dropdown-toggle-nocaret"
                                                href={route("ecommerce.category", category.slug)}
                                            >
                                                {category.name}{" "}
                                                <i className="fa-solid fa-angle-right float-end mt-1"></i>
                                            </Link>
                                            {category.childs.length > 0 && (
                                                <ul className="submenu dropdown-menu">
                                                    {category.childs.map(
                                                        (subcategory) => (
                                                            <li key={subcategory.id} className="nav-item dropdown">
                                                                <Link
                                                                    className="text-decoration-none text-dark nav-item dropdown-item"
                                                                    href={route("ecommerce.category", subcategory.slug)}
                                                                >
                                                                    {subcategory.name}
                                                                    <i className="fa-solid fa-angle-right float-end mt-1"></i>
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
                            </li> */}

                            <li className="nav-item d-md-block d-none category-menu-container">
                                <a className="fw-bold px-0 nav-link d-flex justify-content-start align-items-center" style={{ cursor: "pointer" }} ref={ref} {...anchorProps}>
                                    <i className="bi bi-list fs-5 me-1"></i>
                                    <span>SHOP BY CATEGORIES</span>
                                </a>
                                <ControlledMenu
                                    transition={true}
                                    {...hoverProps}
                                    {...menuState}
                                    anchorRef={ref}
                                    onClose={() => toggle(false)}
                                >
                                    {props.navbar_categories.map((category) => (
                                        <div key={category.id}>
                                            <SubMenu label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", category.slug)}>{category.name}</Link>}>
                                                {category.childs.length > 0 && (
                                                    < >
                                                        {category.childs.map((subcategory) => (
                                                            <div key={subcategory.id}>
                                                                <SubMenu label={<Link className="text-decoration-none text-dark" href={route("ecommerce.category", subcategory.slug)}>{subcategory.name}</Link>}>
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
                            </li>

                            {/* desktop-view */}
                            <form className="d-flex mt-md-0 flex-1 w-100" role="search">
                                <div className="d-flex justify-content-between align-items-center bg-white rounded-5 border border-primary w-100">
                                    <input
                                        value={data.search}
                                        onChange={(e) =>
                                            setData("search", e.target.value)
                                        }
                                        className="form-control  shadow-none border-0 rounded-5 flex-1"
                                        type="search"
                                        placeholder="Search..."
                                        aria-label="Search"
                                    />
                                    <button className="btn btn border-0 sahdow-0" type="submit" onClick={onSubmit}>
                                        <i className="bi bi-search fs-md-5 fs-5 fw-bold text-primary"></i>
                                    </button>
                                </div>
                            </form>
                        </ul>
                        <Link href={route("ecommerce.tracking.index")} className="nav-link nav-item me-5 d-md-block d-none fw-semibold fs-6">
                            <i className="bi bi-truck fs-5 fw-bold"></i><small>TRACK ORDER</small>
                        </Link>
                        <div className="d-md-block d-none ms-4">
                            <MiniCartComponent {...props} />
                        </div>
                    </div>
                </div >
            </nav >
        </>
    );
};

export default NavbarComponent;
