import { Head, Link, useForm, usePage } from '@inertiajs/react';
import React, { useEffect, useState } from 'react'
import { Row } from "react-bootstrap";
import useZiggy from '../../hooks/useZiggy';
import ProductCard from '../../components/ProductCard';
import Form from 'react-bootstrap/Form';
import PageWrapper from '../../layouts/PageWrapper';

const CategoryPage = (props) => {
    const { category, products } = props;
    const { route } = useZiggy();
    const { query_params } = usePage().props;
    const [submit, setSubmit] = useState();
    const { get, processing, data, setData } = useForm({
        sort_order: '',
        ...query_params,
    });

    const handleSortOrder = (e) => {
        setSubmit(true);
        setData(data => ({ ...data, sort_order: e.target.value }));
    }

    useEffect(() => {
        if (submit) {
            get(route('ecommerce.category', category.slug), { preserveScroll: true });
        }
    }, [data])
    

    return (
        <PageWrapper>
            <Head>
                {category.meta_title && <title>{category.meta_title}</title>}
                {category.meta_description && <meta name="description" content={category.meta_description} />}
                {category.meta_keywords && <meta name="keywords" content={category.meta_keywords} />}
            </Head>
            <section className="w-100 py-3 border-bottom border-top d-none d-md-flex" style={{ backgroundColor: '#EAEAEA' }}>
                <div className="main-container px-2">
                    <Link className="text-decoration-none me-2" href={route('ecommerce.home')}><small>Home <i className="fa-solid fa-angles-right fa-sm "></i></small></Link>
                    {
                        category.parent_id && category.childs.length > 0 ? (
                            <>
                                <Link className="text-decoration-none me-2" href={route('ecommerce.category', category.parent.slug)}><small>{category.parent.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                                <small className="d-md-inline-block d-none">{category.name}</small>
                            </>
                        ) : category.parent_id ? (
                            <>
                                <Link className="text-decoration-none me-2" href={route('ecommerce.category', category.parent.parent.slug)}><small>{category.parent.parent.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                                <Link className="text-decoration-none me-2" href={route('ecommerce.category', category.parent.slug)}><small>{category.parent.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                                <small className="d-md-inline-block d-none">{category.name}</small>
                            </>
                        ) : (
                            <small className="d-md-inline-block d-none">{category.name}</small>
                        )
                    }
                </div>
            </section>

            <section className='mt-3'>
                <div className="main-container">
                    <div className="row g-5">
                        <div className="col-md-12">
                            <div>
                                <h4 className='fw-semibold'>{category.name}</h4>
                                <div className='d-flex justify-content-between align-items-center bg-light py-2 px-2 mt-4'>
                                    <span>{products.length} Products</span>
                                    <Form.Select id="filterSelect" onChange={handleSortOrder} defaultValue={data.sort_order} aria-label="Default select example" className="shadow-none rounded-0 category-select">
                                        <option>Select an Option</option>
                                        <option value="high">Sort by price: high to low</option>
                                        <option value="low">Sort by price: low to high</option>
                                    </Form.Select>
                                </div>

                                <Row xs={2} md={4} lg={6} xxl={6} className="g-4 my-4">
                                    {products?.map((record) => (<ProductCard key={record.id} record={record.product} />))}
                                </Row>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PageWrapper>
    )
}

export default CategoryPage
