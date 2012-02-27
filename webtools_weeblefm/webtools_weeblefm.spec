
Name:		webtools_weeblefm
Version:	2.0
Release:	1.ru6
Summary:	weeblefm webtool for Rutgers
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_weeblefm-%version.tgz

Requires: webtools >= 2.0, python-inotify, samba-common, postfix

%description
Core binaries, configs and templates for many Rutgers specific web applications (aka webtools). By default comes with the quota webtool to allow a user to check their quota via the web.

%prep
%setup -q

%build
## Nothing to build 

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT/usr/local/
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/weeblefm
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/weeblefm

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/weeblefm

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog 
%{_libdir}/webtools/html/weeblefm/*
%{_libdir}/webtools/config/weeblefm_config.php.sample
%attr(0500, root, root) %{_libdir}/webtools/webbin/weeblefm

%changelog
* Wed Jan 11 2012 Kaitlin Poskaitis <kap263@nbcs.rutgers.edu> 2.00-1.ru6
- Released for Centos6

* Thu Oct 20 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 2.00-1.ru
- Initial Build
