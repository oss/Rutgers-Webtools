%define prefix /usr/local
%define _libdir %{prefix}/lib64

Name:		webtools_retiree
Version:	2.0
Release:	6.ru6
Summary:	Retiree webtool for Rutgers
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_retiree-%version.tgz

Requires: webtools >= 2.0

%description
Core binaries, configs and templates for many Rutgers specific web applications (aka webtools). By default comes with the quota webtool to allow a user to check their quota via the web.

%prep
%setup -q

%build
## Nothing to build 

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0511 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/retiree

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/webbin/* %{buildroot}%{_libdir}/webtools/webbin
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/retiree

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog
%{_libdir}/webtools/config/retiree_config.php.sample
%attr(511,root,apache) %dir %{_libdir}/webtools/webbin/
%attr(555,root,root) %{_libdir}/webtools/webbin/retiree
%{_libdir}/webtools/html/retiree/index.php
%{_libdir}/webtools/html/retiree/myfunctions.php

%changelog
* Mon Jan 09 2012 Josh Matthews <jam761@nbcs.rutgers.edu> 2.00-6.ru6
- rebuilt for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutger.edu> 2.00-6.ru
- fixed group for /usr/local/lib64/webtools/webbin/retiree from apache to root
- updated source 

* Tue Nov 15 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.00-5.ru
- changed perms for /usr/local/lib64/webtools/webbin/retiree from 644 to 555

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.00-4.ru
- update source
- changed %{_libdir} from /usr/lib64 to /usr/local/lib64

* Mon Sep 19 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 2.00-3.ru
- built with updated source

* Thu Sep 15 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 2.00-2.ru
- Initial Build
