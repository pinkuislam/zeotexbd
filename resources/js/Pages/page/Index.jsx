import React from 'react'
import PageWrapper from '../../layouts/PageWrapper';
import { Link } from '@inertiajs/react';
import useZiggy from '../../hooks/useZiggy';

const DynamicPage = (props) => {
    const { page } = props;
    const { route } = useZiggy();
    return (
        <PageWrapper>
            <section className="w-100 py-3 border-bottom border-top d-none d-md-flex" style={{ backgroundColor: '#EAEAEA' }}>
                <div className="main-container px-2">
                    <Link className="text-decoration-none me-2" href={route('ecommerce.home')}><small>Home <i className="fa-solid fa-angles-right fa-sm "></i></small></Link>
                    <small className="d-md-inline-block d-none">{page.title}</small>
                </div>
            </section>

            <section className='main-container overflow-hidden my-4'>
                <h1 className='text-center border-bottom py-3 text-uppercase mb-4'>{page.title}</h1>
                <div className="row">
                    <div className={page.image_url ? 'col-md-7' : 'col-md-12'}>
                        <p style={{ textAlign: 'justify' }} dangerouslySetInnerHTML={{ __html: page.details }}></p>
                    </div>
                    <div className={page.image_url ? 'col-md-5' : 'col-md-0'}>
                        <img className='img-fluid' src={page.image_url} alt="" srcSet="" />
                    </div>
                </div>
            </section>
        </PageWrapper>
    )
}

export default DynamicPage