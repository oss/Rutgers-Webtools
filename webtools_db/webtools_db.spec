%define prefix /usr/local
%define _libdir %{prefix}/lib64

Name:		webtools_db
Version:	2.0
Release:	1.ru
Summary:    Database for webtools
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_db-%version.tgz

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
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/db

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/db

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog Todo
%{_libdir}/webtools/config/db_config.php.sample
%{_libdir}/webtools/html/db/index.php
%{_libdir}/webtools/html/db/myfunctions.php
%{_libdir}/webtools/html/db/my.js
%{_libdir}/webtools/html/db/showresults.php
%changelog
* Wed Feb 22 2012 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-1.ru
- Initial Build
