import React from 'react';
import {render} from 'react-dom';
import ServiceOption from './ServiceOption';
import Loader from './Loader';

export default class ServicesSelection extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            services: [],
            shownServices: [],
            servicesList: [],
            selectedServices: this.props.selectedServices,
            selectedServicesList: [],
            searchValue: '',
            isLoaded: false
        };
        this.selectService = this.selectService.bind(this);
        this.handleSearchBox = this.handleSearchBox.bind(this);
        this.removeSelectedService = this.removeSelectedService.bind(this);
    }

    componentDidMount()
    {
        this.fetchServiceList(this.state.searchValue);
        this.updateSelectedServiceList();
    }

    fetchServiceList(pattern)
    {
        fetch("/services/search", {
            method: "POST",
            body: JSON.stringify({
                pattern: pattern
            })
        })
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        services: result,
                        shownServices: result,
                        servicesList: this.formAvailableServiceList(result, this.selectService),
                        isLoaded: true
                    });
                },
                (error) => {
                    this.setState({
                        error
                    });
                }
            )
    }

    selectService(service)
    {
        const newList = this.state.selectedServices.slice();
        newList.push(service);

        this.setSelectedServices(newList);
    }

    removeSelectedService(service)
    {
        const newList = this.state.selectedServices.filter((srv) => {
            return srv.id !== service.id;
        });

        this.setSelectedServices(newList);
    }

    setSelectedServices(newList)
    {
        this.setState({
                selectedServices: newList,
                }, () => {
                this.updateSelectedServiceList();
                this.updateServiceList();
            }
        );
        this.props.setSelectedServices(newList);
    }

    updateSelectedServiceList()
    {
        this.setState({
            selectedServicesList: this.formServiceList(this.state.selectedServices, this.removeSelectedService)
        });
    }

    updateServiceList()
    {
        this.setState({
            servicesList: this.formAvailableServiceList(this.state.shownServices, this.selectService)
        });
    }

    formAvailableServiceList(services, onClick)
    {
        if(typeof services !== 'undefined')
            if(services.length > 0)
                return services.map((service, i) => this.formAvailableService(service, i, onClick));
    }

    formAvailableService(service, i, onClick)
    {
        if(!this.isSelected(service))
            return this.formService(service, i, onClick);
    }

    formServiceList(services, onClick)
    {
        if(services.length > 0)
            return services.map((service, i) => this.formService(service, i, onClick));
    }

    formService(service, i, onClick)
    {
        return <ServiceOption onClick={onClick} service={service}
                              key={i}/>;
    }

    isSelected(service)
    {
        return this.state.selectedServices.some((srv) => {
            if(srv.id === service.id)
                return true;
        });
    }

    handleSearchBox(event)
    {
        this.setState({searchValue: event.target.value}, () => {
           // this.fetchServiceList(this.state.searchValue); // may be user in the future
            this.filterServices(this.state.searchValue);
        });
    }

    filterServices(pattern)
    {
        let results = [];
        this.state.services.forEach((service) => {
            if(service.name.indexOf(pattern) !== -1) {
                results.push(service)
            }
        });


        this.setState({
            shownServices: results,
            servicesList: this.formAvailableServiceList(results, this.selectService)
        });
    }

    render(){
        return <div className={'row'}>
            <h1 className={'orderDialogLabel d-flex justify-content-center w-100'}>Paslaugų pasirinkimas</h1>
            <div className={'offset-sm-2 col-sm-8'}>
            { this.state.isLoaded ?
                    <div className={'row serviceSelectionContainer'}>
                        <div className={'col-sm-5'}>
                            <div className={'serviceOptionsContainer'}>
                                <div className={"input-group"}>
                                    <div className={'input-group-prepend'}>
                                        <i className={'input-group-text'}>
                                            &#128269;
                                        </i>
                                    </div>
                                    <input
                                        className={'form-control searchBox'}
                                        type='text'
                                        value={this.state.searchValue}
                                        onChange={this.handleSearchBox}
                                    />
                                </div>
                                <div className={'scrollable-vertical searchableServices'}>
                                    {this.state.servicesList}
                                </div>
                            </div>
                        </div>
                        <div className={'offset-sm-2 col-sm-5'}>
                            <div className={'serviceOptionsContainer scrollable-vertical'}>
                                {this.state.selectedServicesList}
                            </div>
                        </div>
                    </div>
                    :
                    <Loader/>
                }
                </div>
            </div>;
    }
}